<?php
require_once __DIR__ . '/bootstrap.php';
if (!is_logged_in()) {
    redirect_to('login.php');
}

$presentation_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($presentation_id <= 0) {
    http_response_code(400);
    exit('Identifiant invalide.');
}

$stmt = $pdo->prepare('SELECT * FROM presentations WHERE id = ?');
$stmt->execute([$presentation_id]);
$presentation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$presentation || empty($presentation['video_chemin_fichier'])) {
    http_response_code(404);
    exit('Vidéo non trouvée.');
}

$role = current_user()['role'];
$user_id = (int) current_user()['id'];

if ($role === 'etudiant') {
    $has_owner_col = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'id_utilisateur'")->rowCount() > 0;
    if (!$has_owner_col || (int) ($presentation['id_utilisateur'] ?? 0) !== $user_id) {
        http_response_code(403);
        exit('Accès refusé.');
    }
} elseif (!is_organisateur()) {
    http_response_code(403);
    exit('Accès refusé.');
}

$base_dir = rtrim(UPLOADS_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'presentation_videos' . DIRECTORY_SEPARATOR;
$file_path = $base_dir . $presentation['video_chemin_fichier'];
$real_base = realpath($base_dir);
$real_file = realpath($file_path);

if ($real_file === false || $real_base === false || strpos($real_file, $real_base) !== 0 || !is_file($real_file) || !is_readable($real_file)) {
    http_response_code(404);
    exit('Fichier non trouvé.');
}

$ext = strtolower(pathinfo($real_file, PATHINFO_EXTENSION));
$mime_map = [
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'mov' => 'video/quicktime',
    'avi' => 'video/x-msvideo',
    'mkv' => 'video/x-matroska',
];
$content_type = $mime_map[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . basename($presentation['video_nom_fichier'] ?? 'video.' . $ext) . '"');
header('Content-Length: ' . filesize($real_file));
header('Cache-Control: must-revalidate');
header('Pragma: public');

readfile($real_file);
exit;
