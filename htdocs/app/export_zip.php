<?php
/**
 * Export ZIP d'une édition : projets triés par nom, rapports + PPT + vidéos.
 * ?mode=info  → JSON (vérification avant téléchargement)
 * sinon       → fichier ZIP (téléchargement direct navigateur)
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

@ini_set('memory_limit', '512M');
@set_time_limit(600);
@ignore_user_abort(true);

if (ob_get_level()) {
    ob_end_clean();
}

function export_json_error(int $code, string $message, ?string $detail = null): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    $payload = ['ok' => false, 'error' => $message];
    if ($detail !== null) {
        $payload['detail'] = $detail;
    }
    echo json_encode($payload);
    exit;
}

if (!is_logged_in() || !is_organisateur()) {
    export_json_error(403, 'Accès non autorisé. Connectez-vous en tant qu\'organisateur.');
}

$edition_id = (int) ($_GET['edition_id'] ?? 0);
$mode = $_GET['mode'] ?? 'download';

if ($edition_id <= 0) {
    export_json_error(400, 'edition_id invalide.');
}

if (!class_exists('ZipArchive')) {
    export_json_error(500, 'Extension ZipArchive indisponible sur ce serveur.');
}

function zip_safe_name(string $name, string $fallback = 'Sans_nom'): string
{
    $name = trim($name);
    if ($name === '') {
        return $fallback;
    }
    $name = preg_replace('/\s+/', '_', $name);
    $name = preg_replace('/[^a-zA-Z0-9_\-\.]/u', '', $name);

    return $name !== '' ? $name : $fallback;
}

function path_is_inside_dir(string $filePath, string $dirPath): bool
{
    $realFile = realpath($filePath);
    $realDir = realpath($dirPath);
    if ($realFile === false || $realDir === false) {
        return false;
    }
    $realDir = rtrim(str_replace('\\', '/', $realDir), '/') . '/';
    $realFile = str_replace('\\', '/', $realFile);

    return stripos($realFile, $realDir) === 0;
}

function resolve_upload_file(string $subdir, ?string $storedName): ?string
{
    $storedName = basename((string) $storedName);
    if ($storedName === '') {
        return null;
    }

    $base = rtrim(UPLOADS_DIR, '/\\') . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR;
    if (!is_dir($base)) {
        return null;
    }

    $fullPath = $base . $storedName;
    if (!is_file($fullPath) || !is_readable($fullPath)) {
        return null;
    }
    if (!path_is_inside_dir($fullPath, $base)) {
        return null;
    }

    return realpath($fullPath) ?: $fullPath;
}

function zip_unique_internal_path(array &$usedPaths, string $desiredPath): string
{
    if (!isset($usedPaths[$desiredPath])) {
        $usedPaths[$desiredPath] = true;
        return $desiredPath;
    }
    $info = pathinfo($desiredPath);
    $dir = ($info['dirname'] ?? '') !== '.' ? $info['dirname'] . '/' : '';
    $base = $info['filename'] ?? 'fichier';
    $ext = isset($info['extension']) ? '.' . $info['extension'] : '';
    $n = 2;
    do {
        $candidate = $dir . $base . '_' . $n . $ext;
        $n++;
    } while (isset($usedPaths[$candidate]));

    $usedPaths[$candidate] = true;

    return $candidate;
}

/**
 * @return array{files: list<array{path: string, zip_path: string, size: int}>, stats: array, annee: string|int, download_name: string}
 */
function collect_edition_export_files(PDO $pdo, int $edition_id): array
{
    $stmt = $pdo->prepare('SELECT id, annee FROM editions WHERE id = ?');
    $stmt->execute([$edition_id]);
    $edition = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$edition) {
        throw new RuntimeException('Édition introuvable.');
    }

    $annee = $edition['annee'];
    $has_nom_groupe = $pdo->query("SHOW COLUMNS FROM projets LIKE 'nom_groupe'")->rowCount() > 0;
    $groupeCol = $has_nom_groupe ? 'p.nom_groupe' : "'' AS nom_groupe";

    $stmt = $pdo->prepare("
        SELECT p.id, p.nom, {$groupeCol}
        FROM projets p
        WHERE p.id_edition = ?
        ORDER BY p.nom ASC, p.id ASC
    ");
    $stmt->execute([$edition_id]);
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $has_video_col = false;
    $presentations_exists = $pdo->query("SHOW TABLES LIKE 'presentations'")->rowCount() > 0;
    if ($presentations_exists) {
        $has_video_col = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'video_chemin_fichier'")->rowCount() > 0;
    }

    $files = [];
    $usedPaths = [];
    $stats = ['projets' => count($projets), 'trouves' => 0, 'manquants' => 0, 'size_bytes' => 0];
    $editionFolder = 'SEIC_Edition_' . zip_safe_name((string) $annee, 'Edition');

    $projetIndex = 0;
    foreach ($projets as $projet) {
        $projetIndex++;
        $projetId = (int) $projet['id'];
        $projetFolder = sprintf('%02d_%s', $projetIndex, zip_safe_name($projet['nom'], 'Projet_' . $projetId));
        $groupeLabel = zip_safe_name((string) ($projet['nom_groupe'] ?? ''), 'Sans_groupe');

        $stmtR = $pdo->prepare('
            SELECT id, fichier FROM rapports WHERE id_projet = ?
            ORDER BY date_soumission ASC, id ASC
        ');
        $stmtR->execute([$projetId]);
        foreach ($stmtR->fetchAll(PDO::FETCH_ASSOC) as $rapport) {
            $disk = resolve_upload_file('rapports', $rapport['fichier']);
            if ($disk === null) {
                $stats['manquants']++;
                continue;
            }
            $stats['trouves']++;
            $size = filesize($disk) ?: 0;
            $stats['size_bytes'] += $size;
            $displayName = zip_safe_name(basename((string) $rapport['fichier']), 'rapport_' . $rapport['id'] . '.pdf');
            $files[] = [
                'path' => $disk,
                'zip_path' => zip_unique_internal_path($usedPaths, "{$editionFolder}/{$projetFolder}/Groupe_{$groupeLabel}/rapports/{$displayName}"),
                'size' => $size,
            ];
        }

        if (!$presentations_exists) {
            continue;
        }

        $videoCols = $has_video_col
            ? ', pr.video_chemin_fichier, pr.video_nom_fichier'
            : ', NULL AS video_chemin_fichier, NULL AS video_nom_fichier';

        $stmtP = $pdo->prepare("
            SELECT pr.id, pr.chemin_fichier, pr.nom_fichier{$videoCols}
            FROM presentations pr WHERE pr.id_projet = ?
            ORDER BY pr.date_soumission ASC, pr.id ASC
        ");
        $stmtP->execute([$projetId]);

        foreach ($stmtP->fetchAll(PDO::FETCH_ASSOC) as $pres) {
            $pptDisk = resolve_upload_file('presentations', $pres['chemin_fichier']);
            if ($pptDisk !== null) {
                $stats['trouves']++;
                $size = filesize($pptDisk) ?: 0;
                $stats['size_bytes'] += $size;
                $pptName = zip_safe_name(
                    (string) ($pres['nom_fichier'] ?? basename((string) $pres['chemin_fichier'])),
                    'presentation_' . $pres['id'] . '.pptx'
                );
                $files[] = [
                    'path' => $pptDisk,
                    'zip_path' => zip_unique_internal_path($usedPaths, "{$editionFolder}/{$projetFolder}/Groupe_{$groupeLabel}/presentations/{$pptName}"),
                    'size' => $size,
                ];
            } else {
                $stats['manquants']++;
            }

            if ($has_video_col && !empty($pres['video_chemin_fichier'])) {
                $videoDisk = resolve_upload_file('presentation_videos', $pres['video_chemin_fichier']);
                if ($videoDisk !== null) {
                    $stats['trouves']++;
                    $size = filesize($videoDisk) ?: 0;
                    $stats['size_bytes'] += $size;
                    $videoName = zip_safe_name(
                        (string) ($pres['video_nom_fichier'] ?? basename((string) $pres['video_chemin_fichier'])),
                        'video_' . $pres['id'] . '.mp4'
                    );
                    $files[] = [
                        'path' => $videoDisk,
                        'zip_path' => zip_unique_internal_path($usedPaths, "{$editionFolder}/{$projetFolder}/Groupe_{$groupeLabel}/videos/{$videoName}"),
                        'size' => $size,
                    ];
                } else {
                    $stats['manquants']++;
                }
            }
        }
    }

    $download_name = $editionFolder . '.zip';

    return [
        'files' => $files,
        'stats' => $stats,
        'annee' => $annee,
        'download_name' => $download_name,
    ];
}

try {
    $export = collect_edition_export_files($pdo, $edition_id);
    $files = $export['files'];
    $stats = $export['stats'];

    if (empty($files)) {
        export_json_error(
            404,
            'Aucun fichier disponible sur le serveur pour cette édition.',
            sprintf(
                '%d projet(s), %d référence(s) manquante(s) sur le disque.',
                $stats['projets'],
                $stats['manquants']
            )
        );
    }

    if ($mode === 'info') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'files' => count($files),
            'projets' => $stats['projets'],
            'manquants' => $stats['manquants'],
            'size_mb' => round($stats['size_bytes'] / 1024 / 1024, 1),
            'download_name' => $export['download_name'],
            'annee' => $export['annee'],
        ]);
        exit;
    }

    $zip_path = tempnam(sys_get_temp_dir(), 'seic_zip_');
    if ($zip_path === false) {
        throw new RuntimeException('Impossible de créer un fichier temporaire.');
    }
    $zip_file = $zip_path . '.zip';
    @unlink($zip_path);

    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Impossible de créer le ZIP.');
    }

    foreach ($files as $f) {
        if (!$zip->addFile($f['path'], $f['zip_path'])) {
            $zip->close();
            @unlink($zip_file);
            throw new RuntimeException('Impossible d\'ajouter : ' . basename($f['path']));
        }
    }
    $zip->close();

    if (!is_file($zip_file) || filesize($zip_file) === 0) {
        @unlink($zip_file);
        throw new RuntimeException('ZIP vide après création.');
    }

    $download_name = $export['download_name'];
    $zipSize = filesize($zip_file);

    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $download_name . '"');
    header('Content-Length: ' . $zipSize);
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: public');
    header('X-Export-Files-Count: ' . count($files));
    header('X-Export-Missing-Count: ' . $stats['manquants']);

    $handle = fopen($zip_file, 'rb');
    if ($handle === false) {
        @unlink($zip_file);
        throw new RuntimeException('Impossible de lire le ZIP.');
    }
    while (!feof($handle)) {
        echo fread($handle, 1024 * 1024);
        flush();
    }
    fclose($handle);
    @unlink($zip_file);
    exit;
} catch (Throwable $e) {
    error_log('export_zip: ' . $e->getMessage());
    $detail = (defined('APP_ENV') && APP_ENV !== 'production') ? $e->getMessage() : null;
    export_json_error(500, 'Erreur serveur lors de la création du ZIP.', $detail);
}
