<?php
require_once __DIR__ . '/bootstrap.php';
if (!is_logged_in() || !is_organisateur()) {
    redirect_to_login();
}

$presentation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($presentation_id <= 0) {
    header('Location: gestion_powerpoint.php');
    exit;
}

// Récupérer les informations de la présentation
$stmt = $pdo->prepare('
    SELECT 
        p.*,
        pr.nom as nom_projet,
        pr.nom_groupe
    FROM presentations p
    JOIN projets pr ON p.id_projet = pr.id
    WHERE p.id = ?
');
$stmt->execute([$presentation_id]);
$presentation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$presentation) {
    header('Location: gestion_powerpoint.php');
    exit;
}

$file_path = rtrim(UPLOADS_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'presentations' . DIRECTORY_SEPARATOR . $presentation['chemin_fichier'];
$real_base = realpath(rtrim(UPLOADS_DIR, DIRECTORY_SEPARATOR));
$real_file = realpath($file_path);

if ($real_file === false || $real_base === false || strpos($real_file, $real_base) !== 0 || !is_file($real_file) || !is_readable($real_file)) {
    http_response_code(404);
    exit('Fichier non trouvé.');
}

// Définir les headers pour le téléchargement
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $presentation['nom_fichier'] . '"');
header('Content-Length: ' . filesize($real_file));
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Lire et envoyer le fichier
readfile($real_file);
exit;








