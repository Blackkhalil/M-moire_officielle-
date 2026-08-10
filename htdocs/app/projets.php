<?php
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || !is_organisateur()) {
    redirect_to('login.php');
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/commentaires.php';

// Ajout d'un projet
if (isset($_POST['ajouter'])) {
    require_valid_csrf();
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $membres = $_POST['membres'] ?? '';
    $id_edition = $_POST['id_edition'] ?? null;
    $stmt = $pdo->prepare('INSERT INTO projets (nom, description, categorie, membres, id_edition) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$nom, $description, $categorie, $membres, $id_edition]);
}
// Suppression d'un projet
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $pdo->prepare('DELETE FROM projets WHERE id = ?')->execute([$id]);
}
// Récupération des projets et éditions
$projets = $pdo->query('SELECT * FROM projets ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les commentaires pour chaque projet
foreach ($projets as &$projet) {
    $projet['commentaires'] = getCommentairesProjet($pdo, $projet['id']);
    $projet['nb_commentaires'] = countCommentairesProjet($pdo, $projet['id']);
}
unset($projet); 