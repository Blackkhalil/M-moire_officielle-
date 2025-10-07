<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$success = '';
$error = '';
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $membres = $_POST['membres'] ?? '';
    $id_edition = $_POST['id_edition'] ?? null;
    if ($nom && $id_edition) {
        $stmt = $pdo->prepare('INSERT INTO projets (nom, description, categorie, membres, id_edition) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$nom, $description, $categorie, $membres, $id_edition])) {
            $success = 'Projet soumis avec succès !';
        } else {
            $error = 'Erreur lors de la soumission.';
        }
    } else {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    }
} 