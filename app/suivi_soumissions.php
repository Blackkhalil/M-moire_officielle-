<?php
/**
 * Backend pour le suivi des soumissions par les étudiants
 * 
 * Ce fichier récupère toutes les données nécessaires pour que les étudiants puissent :
 * - Voir leurs rapports soumis et leur statut de validation
 * - Voir leurs présentations PowerPoint et leur statut
 * - Consulter les statistiques de leurs soumissions
 * - Recevoir des commentaires des organisateurs
 * 
 * @author Khalil
 * @version 1.0
 */

// On vérifie que l'utilisateur est connecté et qu'il est étudiant
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$user = current_user();
$user_nom = $user['nom'];

// Récupérer les projets de l'étudiant
$projets_stmt = $pdo->prepare("SELECT id, nom FROM projets WHERE membres LIKE ? ORDER BY nom");
$projets_stmt->execute(['%' . $user_nom . '%']);
$projets = $projets_stmt->fetchAll(PDO::FETCH_ASSOC);

$projets_ids = array_column($projets, 'id');

// Récupérer les rapports de l'étudiant
$rapports = [];
if (!empty($projets_ids)) {
    $placeholders = str_repeat('?,', count($projets_ids) - 1) . '?';
    $rapports_stmt = $pdo->prepare("
        SELECT 
            r.*,
            p.nom as projet_nom,
            p.categorie,
            e.annee
        FROM rapports r
        JOIN projets p ON r.id_projet = p.id
        JOIN editions e ON p.id_edition = e.id
        WHERE r.id_projet IN ($placeholders)
        ORDER BY r.date_soumission DESC
    ");
    $rapports_stmt->execute($projets_ids);
    $rapports = $rapports_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les présentations de l'étudiant (si la table existe)
$presentations = [];
if (!empty($projets_ids)) {
    // Vérifier si la table presentations existe
    $table_exists = $pdo->query("SHOW TABLES LIKE 'presentations'")->rowCount() > 0;
    
    if ($table_exists) {
        $placeholders = str_repeat('?,', count($projets_ids) - 1) . '?';
        $presentations_stmt = $pdo->prepare("
            SELECT 
                pr.*,
                p.nom as projet_nom,
                p.categorie,
                e.annee
            FROM presentations pr
            JOIN projets p ON pr.id_projet = p.id
            JOIN editions e ON p.id_edition = e.id
            WHERE pr.id_projet IN ($placeholders)
            ORDER BY pr.date_soumission DESC
        ");
        $presentations_stmt->execute($projets_ids);
        $presentations = $presentations_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Statistiques
$stats = [
    'rapports_total' => count($rapports),
    'rapports_valides' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'valide')),
    'rapports_en_attente' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'en_attente')),
    'rapports_refuses' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'refuse')),
    'presentations_total' => count($presentations),
    'presentations_valides' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'valide')),
    'presentations_en_attente' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'en_attente')),
    'presentations_refusees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'refuse'))
];
?>
