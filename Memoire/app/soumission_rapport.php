<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$success = '';
$error = '';

// Récupérer les projets de l'étudiant (où il est membre)
$user = current_user();
$projets = $pdo->prepare("SELECT * FROM projets WHERE membres LIKE ?");
$projets->execute(['%' . $user['nom'] . '%']);
$projets = $projets->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_projet = $_POST['id_projet'] ?? null;
    if ($id_projet && isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['fichier'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $error = 'Le fichier doit être un PDF.';
        } else {
            $filename = uniqid('rapport_') . '.pdf';
            $dest = UPLOADS_DIR . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $stmt = $pdo->prepare('INSERT INTO rapports (id_projet, date_soumission, fichier) VALUES (?, NOW(), ?)');
                if ($stmt->execute([$id_projet, $filename])) {
                    $success = 'Rapport soumis avec succès !';
                } else {
                    $error = 'Erreur lors de l\'enregistrement en base.';
                }
            } else {
                $error = 'Erreur lors de l\'upload du fichier.';
            }
        }
    } else {
        $error = 'Veuillez sélectionner un projet et un fichier PDF.';
    }
} 