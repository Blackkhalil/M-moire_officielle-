<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

// Ajout d'une édition
if (isset($_POST['ajouter'])) {
    $annee = $_POST['annee'] ?? '';
    $description = $_POST['description'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $stmt = $pdo->prepare('INSERT INTO editions (annee, description, date_debut, date_fin) VALUES (?, ?, ?, ?)');
    $stmt->execute([$annee, $description, $date_debut, $date_fin]);
}
// Suppression d'une édition
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $pdo->prepare('DELETE FROM editions WHERE id = ?')->execute([$id]);
}
// Récupération des éditions
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC); 