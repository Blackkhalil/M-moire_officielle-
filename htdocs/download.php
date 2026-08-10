<?php
require_once __DIR__ . '/bootstrap.php';

if (!is_logged_in()) {
	redirect_to_login();
}

$user = current_user();
$role = $user['role'] ?? 'visiteur';

// Only organisateur can download any report; students could be limited (future)
if (!in_array($role, ['organisateur'], true)) {
	http_response_code(403);
	echo 'Accès non autorisé';
	exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$inline = isset($_GET['inline']);
if ($id <= 0) {
	http_response_code(400);
	echo 'Paramètre invalide';
	exit;
}

try {
	$stmt = $pdo->prepare('SELECT r.fichier, p.nom AS projet_nom FROM rapports r JOIN projets p ON r.id_projet = p.id WHERE r.id = ?');
	$stmt->execute([$id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!$row) {
		http_response_code(404);
		echo 'Rapport introuvable';
		exit;
	}
	$filename = $row['fichier'];
	$displayName = preg_replace('/\s+/', '_', $row['projet_nom']) . '.pdf';
	$filePath = rtrim(UPLOADS_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'rapports' . DIRECTORY_SEPARATOR . $filename;
	
	// Security: ensure file is under uploads directory
	$realBase = realpath(UPLOADS_DIR);
	$realFile = realpath($filePath);
	if ($realFile === false || strpos($realFile, $realBase) !== 0) {
		http_response_code(403);
		echo 'Accès refusé';
		exit;
	}
	if (!is_file($realFile) || !is_readable($realFile)) {
		http_response_code(404);
		echo 'Fichier non trouvé';
		exit;
	}
	
	// Send headers
	header('Content-Description: File Transfer');
	header('Content-Type: application/pdf');
    header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $displayName . '"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize($realFile));
	header('Cache-Control: no-store');
	flush();
	readfile($realFile);
	exit;
} catch (Throwable $e) {
	http_response_code(500);
	echo 'Erreur serveur';
	exit;
}









