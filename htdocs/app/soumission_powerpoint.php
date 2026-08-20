<?php
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    redirect_to_login();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/upload_config.php';

const PRESENTATION_VIDEO_MAX_BYTES = 100 * 1024 * 1024;
const PRESENTATION_VIDEO_EXTENSIONS = ['mp4', 'webm', 'mov', 'avi', 'mkv'];

function ensure_presentation_video_schema(PDO $pdo): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $table_exists = $pdo->query("SHOW TABLES LIKE 'presentations'")->rowCount() > 0;
    if (!$table_exists) {
        return;
    }
    if ($pdo->query("SHOW COLUMNS FROM presentations LIKE 'video_chemin_fichier'")->rowCount() === 0) {
        $pdo->exec(
            'ALTER TABLE presentations
                ADD COLUMN video_nom_fichier VARCHAR(255) NULL AFTER taille_fichier,
                ADD COLUMN video_chemin_fichier VARCHAR(500) NULL AFTER video_nom_fichier,
                ADD COLUMN video_taille_fichier INT NULL AFTER video_chemin_fichier'
        );
    }
    $checked = true;
}

function presentation_upload_error_message(int $error_code): string
{
    $error_messages = [
        UPLOAD_ERR_INI_SIZE => 'Le fichier est trop volumineux (limite PHP: ' . ini_get('upload_max_filesize') . ', POST: ' . ini_get('post_max_size') . ').',
        UPLOAD_ERR_FORM_SIZE => 'Le fichier est trop volumineux (limite formulaire: ' . ini_get('post_max_size') . ').',
        UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
        UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
        UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque.',
        UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement.',
    ];

    return $error_messages[$error_code] ?? 'Erreur inconnue lors du téléchargement.';
}

function presentation_video_provided(array $file): bool
{
    return isset($file['error'])
        && $file['error'] !== UPLOAD_ERR_NO_FILE
        && ($file['name'] ?? '') !== '';
}

function validate_presentation_video(array $file): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return presentation_upload_error_message($file['error']);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, PRESENTATION_VIDEO_EXTENSIONS, true)) {
        return 'Formats vidéo acceptés : .mp4, .webm, .mov, .avi, .mkv';
    }
    if ($file['size'] > PRESENTATION_VIDEO_MAX_BYTES) {
        return 'La vidéo ne doit pas dépasser 100 Mo.';
    }
    if ($file['size'] === 0) {
        return 'Le fichier vidéo est vide ou corrompu.';
    }

    return null;
}

$success = '';
$error = '';

ensure_presentation_video_schema($pdo);

$user_id = current_user()['id'];
$projets = $pdo->query('SELECT * FROM projets ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $id_projet_raw = $_POST['id_projet'] ?? null;
    $id_projet = null;

    if ($id_projet_raw === null || $id_projet_raw === '') {
        $error = 'Veuillez sélectionner un projet.';
    } elseif (!ctype_digit((string) $id_projet_raw)) {
        $error = 'Projet invalide.';
    } elseif (!isset($_FILES['presentation'])) {
        $error = 'Aucun fichier PowerPoint n\'a été sélectionné.';
    } elseif ($_FILES['presentation']['error'] !== UPLOAD_ERR_OK) {
        $error = presentation_upload_error_message((int) $_FILES['presentation']['error']);
    } else {
        $id_projet = (int) $id_projet_raw;
        $stmtProjet = $pdo->prepare('SELECT id FROM projets WHERE id = ?');
        $stmtProjet->execute([$id_projet]);
        if (!$stmtProjet->fetchColumn()) {
            $error = 'Projet introuvable.';
        }
    }

    if (!$error && presentation_video_provided($_FILES['video_projet'] ?? [])) {
        $video_error = validate_presentation_video($_FILES['video_projet']);
        if ($video_error !== null) {
            $error = $video_error;
        }
    }

    if (!$error) {
        $file = $_FILES['presentation'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['ppt', 'pptx'];

        if (!in_array($extension, $allowed_extensions, true)) {
            $error = 'Seuls les fichiers PowerPoint (.ppt et .pptx) sont autorisés.';
        } elseif ($file['size'] > 50 * 1024 * 1024) {
            $error = 'Le fichier PowerPoint ne doit pas dépasser 50 Mo.';
        } elseif ($file['size'] === 0) {
            $error = 'Le fichier PowerPoint est vide ou corrompu.';
        } else {
            $upload_dir = rtrim(UPLOADS_DIR, '/\\') . DIRECTORY_SEPARATOR . 'presentations' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename = 'presentation_' . $id_projet . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $error = 'Erreur lors du téléchargement du fichier PowerPoint.';
            } else {
                $has_user_col = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'id_utilisateur'")->rowCount() > 0;
                $has_video_col = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'video_chemin_fichier'")->rowCount() > 0;

                if ($has_user_col) {
                    $stmt = $pdo->prepare(
                        'INSERT INTO presentations (id_projet, id_utilisateur, nom_fichier, chemin_fichier, taille_fichier)
                         VALUES (?, ?, ?, ?, ?)'
                    );
                    $stmt->execute([$id_projet, $user_id, $file['name'], $filename, $file['size']]);
                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO presentations (id_projet, nom_fichier, chemin_fichier, taille_fichier)
                         VALUES (?, ?, ?, ?)'
                    );
                    $stmt->execute([$id_projet, $file['name'], $filename, $file['size']]);
                }

                if ($stmt->rowCount() <= 0) {
                    $error = 'Erreur lors de l\'enregistrement en base de données.';
                    unlink($filepath);
                } else {
                    $presentation_id = (int) $pdo->lastInsertId();
                    $video_uploaded = false;

                    if ($has_video_col && presentation_video_provided($_FILES['video_projet'] ?? [])) {
                        $video = $_FILES['video_projet'];
                        $video_ext = strtolower(pathinfo($video['name'], PATHINFO_EXTENSION));
                        $video_dir = rtrim(UPLOADS_DIR, '/\\') . DIRECTORY_SEPARATOR . 'presentation_videos' . DIRECTORY_SEPARATOR;
                        if (!is_dir($video_dir)) {
                            mkdir($video_dir, 0755, true);
                        }

                        $video_filename = 'video_' . $id_projet . '_' . time() . '.' . $video_ext;
                        $video_path = $video_dir . $video_filename;

                        if (move_uploaded_file($video['tmp_name'], $video_path)) {
                            $update = $pdo->prepare(
                                'UPDATE presentations
                                 SET video_nom_fichier = ?, video_chemin_fichier = ?, video_taille_fichier = ?
                                 WHERE id = ?'
                            );
                            $update->execute([$video['name'], $video_filename, $video['size'], $presentation_id]);
                            $video_uploaded = true;
                        } else {
                            $error = 'La présentation a été enregistrée, mais la vidéo n\'a pas pu être téléchargée.';
                        }
                    }

                    if (!$error) {
                        $success = $video_uploaded
                            ? 'Présentation et vidéo du projet soumises avec succès !'
                            : 'Présentation soumise avec succès !';

                        require_once __DIR__ . '/mailer.php';
                        $projetNomStmt = $pdo->prepare('SELECT nom FROM projets WHERE id = ?');
                        $projetNomStmt->execute([$id_projet]);
                        $projetNom = (string) ($projetNomStmt->fetchColumn() ?: 'Projet #' . $id_projet);

                        send_submission_notification_to_organizers(
                            $pdo,
                            'powerpoint',
                            (string) (current_user()['nom'] ?? ''),
                            $projetNom,
                            mail_url_to('gestion_powerpoint.php')
                        );
                    }
                }
            }
        }
    }
}
