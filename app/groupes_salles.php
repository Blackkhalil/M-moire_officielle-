<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

// Ajout d'une salle
if (isset($_POST['ajouter_salle'])) {
    $nom = $_POST['nom_salle'] ?? '';
    $localisation = $_POST['localisation'] ?? '';
    if ($nom) {
        $stmt = $pdo->prepare('INSERT INTO salles (nom, localisation) VALUES (?, ?)');
        $stmt->execute([$nom, $localisation]);
    }
}
// Suppression d'une salle
if (isset($_GET['supprimer_salle'])) {
    $id = (int)$_GET['supprimer_salle'];
    $pdo->prepare('DELETE FROM salles WHERE id = ?')->execute([$id]);
}
// Ajout d'un groupe
if (isset($_POST['ajouter_groupe'])) {
    $nom = $_POST['nom_groupe'] ?? '';
    $id_edition = $_POST['id_edition'] ?? null;
    $id_salle = $_POST['id_salle'] ?? null;
    if ($nom && $id_edition && $id_salle) {
        $stmt = $pdo->prepare('INSERT INTO groupes (nom, id_edition, id_salle) VALUES (?, ?, ?)');
        $stmt->execute([$nom, $id_edition, $id_salle]);
    }
}
// Suppression d'un groupe
if (isset($_GET['supprimer_groupe'])) {
    $id = (int)$_GET['supprimer_groupe'];
    $pdo->prepare('DELETE FROM groupes WHERE id = ?')->execute([$id]);
}
// Récupération des données
$salles = $pdo->query('SELECT * FROM salles ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);
$groupes = $pdo->query('SELECT g.*, s.nom AS salle_nom, e.annee FROM groupes g JOIN salles s ON g.id_salle = s.id JOIN editions e ON g.id_edition = e.id ORDER BY g.nom')->fetchAll(PDO::FETCH_ASSOC); 