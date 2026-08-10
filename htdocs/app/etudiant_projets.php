<?php
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    redirect_to('login.php');
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/commentaires.php';

$user = current_user();
$user_nom = trim($user['nom']);

// Récupérer toutes les éditions
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les données pour l'affichage
$edition_id = $_GET['edition'] ?? null;
$projets_data = [];

if ($edition_id) {
    // Récupérer TOUS les projets de l'édition
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
    $all_projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Filtrer pour ne garder que les projets où l'étudiant est membre
    foreach ($all_projets as $projet) {
        if (empty($projet['membres'])) {
            continue;
        }
        
        // Normaliser la liste des membres
        $membres_raw = str_replace(["\r\n", "\n", "\r"], ',', $projet['membres']);
        $membres_list = array_map('trim', explode(',', $membres_raw));
        $membres_list = array_filter($membres_list, function($m) { 
            return !empty(trim($m)) && trim($m) !== '';
        });
        
        // Vérifier si l'étudiant est membre du projet
        $est_membre = false;
        foreach ($membres_list as $membre) {
            if (strcasecmp(trim($membre), $user_nom) === 0) {
                $est_membre = true;
                break;
            }
        }
        
        // Si l'étudiant est membre, ajouter le projet avec ses commentaires
        if ($est_membre) {
            $projet['commentaires'] = getCommentairesProjet($pdo, $projet['id']);
            $projet['nb_commentaires'] = countCommentairesProjet($pdo, $projet['id']);
            $projets_data[] = $projet;
        }
    }
}
?>
