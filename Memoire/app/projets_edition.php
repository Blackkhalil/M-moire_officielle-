<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$edition_id = $_GET['id'] ?? null;
$edition = null;
$projets = [];

if ($edition_id) {
    // Récupérer les informations de l'édition
    $stmt = $pdo->prepare('SELECT * FROM editions WHERE id = ?');
    $stmt->execute([$edition_id]);
    $edition = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($edition) {
        // Récupérer tous les projets de cette édition
        $stmt = $pdo->prepare('SELECT * FROM projets WHERE id_edition = ? ORDER BY nom');
        $stmt->execute([$edition_id]);
        $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Récupérer toutes les éditions pour le menu
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);
?>