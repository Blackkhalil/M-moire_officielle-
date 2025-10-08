<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$presentation_id = $_GET['id'] ?? null;

if (!$presentation_id) {
    header('Location: gestion_powerpoint.php');
    exit;
}

// Récupérer les informations de la présentation
$stmt = $pdo->prepare('
    SELECT p.*, pr.nom as nom_projet 
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

$file_path = $presentation['chemin_fichier'];

// Vérifier que le fichier existe
if (!file_exists($file_path)) {
    die('Fichier non trouvé.');
}

// Définir les headers pour le téléchargement
$file_name = $presentation['nom_fichier'];
$file_size = filesize($file_path);
$file_type = $presentation['type_mime'];

// Headers pour forcer le téléchargement
header('Content-Type: ' . $file_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Lire et envoyer le fichier
readfile($file_path);
exit;
?>
