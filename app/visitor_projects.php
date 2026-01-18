<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'visiteur') {
    header('Location: ../htdocs/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/commentaires.php';

// Traitement des commentaires (fait dans commentaires.php qui est inclus)
// Les variables $success et $error sont définies dans commentaires.php

// Rediriger après succès pour éviter la resoumission du formulaire
if (isset($success) && $success && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $edition_param = isset($_GET['edition']) ? '?edition=' . urlencode($_GET['edition']) : '';
    header('Location: visitor_projects.php' . $edition_param);
    exit;
}

// Récupérer toutes les éditions
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les données pour l'affichage
$edition_id = $_GET['edition'] ?? null;
$projets_data = [];

if ($edition_id) {
    // Récupérer les projets avec les informations des groupes et salles
    $stmt = $pdo->prepare('
        SELECT 
            p.*,
            g.nom as nom_groupe_complet,
            s.nom as nom_salle,
            s.localisation,
            e.annee
        FROM projets p
        LEFT JOIN groupes g ON p.nom_groupe = g.nom AND p.id_edition = g.id_edition
        LEFT JOIN salles s ON g.id_salle = s.id
        LEFT JOIN editions e ON p.id_edition = e.id
        WHERE p.id_edition = ?
        ORDER BY s.nom, p.nom
    ');
    $stmt->execute([$edition_id]);
    $projets_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les commentaires pour chaque projet
    foreach ($projets_data as &$projet) {
        $projet['commentaires'] = getCommentairesProjet($pdo, $projet['id']);
        $projet['nb_commentaires'] = countCommentairesProjet($pdo, $projet['id']);
    }
    unset($projet);
}
?>