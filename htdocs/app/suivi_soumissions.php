<?php
/**
 * Backend pour le suivi des soumissions par les étudiants
 * VERSION SIMPLIFIÉE ET FONCTIONNELLE
 */

// Vérification de l'authentification
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    redirect_to_login();
}
require_once __DIR__ . '/../config/config.php';

$user = current_user();
$user_id = $user['id'];
$user_nom = trim($user['nom']);

// Initialisation
$rapports = [];
$presentations = [];
$debug_info = []; // Pour le debug

// ============================================
// RÉCUPÉRATION DES RAPPORTS
// ============================================

// Méthode 1 : Par id_utilisateur (si disponible)
try {
    $has_col = $pdo->query("SHOW COLUMNS FROM rapports LIKE 'id_utilisateur'")->rowCount() > 0;
    if ($has_col) {
        $stmt = $pdo->prepare("
            SELECT r.*, p.nom as projet_nom, p.categorie, e.annee
            FROM rapports r
            JOIN projets p ON r.id_projet = p.id
            LEFT JOIN editions e ON p.id_edition = e.id
            WHERE r.id_utilisateur = ?
            ORDER BY r.date_soumission DESC
        ");
        $stmt->execute([$user_id]);
        $rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $debug_info[] = "Méthode 1 (id_utilisateur): " . count($rapports) . " rapports trouvés";
    }
} catch (Exception $e) {
    $debug_info[] = "Erreur méthode 1: " . $e->getMessage();
}

// Si aucun rapport trouvé, utiliser la méthode par projets
if (empty($rapports)) {
    try {
        // Récupérer tous les projets
        $all_projets = $pdo->query("SELECT id, nom, membres FROM projets")->fetchAll(PDO::FETCH_ASSOC);
        $projets_ids = [];
        
        // Chercher les projets où l'utilisateur est membre
        foreach ($all_projets as $projet) {
            if (empty($projet['membres'])) continue;
            
            // Normaliser les membres
            $membres = str_replace(["\r\n", "\n", "\r"], ',', $projet['membres']);
            $membres_list = array_map('trim', explode(',', $membres));
            $membres_list = array_filter($membres_list);
            
            // Vérifier si le nom de l'utilisateur est dans la liste
            foreach ($membres_list as $membre) {
                if (strcasecmp(trim($membre), $user_nom) === 0) {
                    $projets_ids[] = $projet['id'];
                    break;
                }
            }
        }
        
        $debug_info[] = "Projets trouvés pour " . $user_nom . ": " . count($projets_ids);
        
        // Récupérer les rapports de ces projets
        if (!empty($projets_ids)) {
            $placeholders = str_repeat('?,', count($projets_ids) - 1) . '?';
            $stmt = $pdo->prepare("
                SELECT r.*, p.nom as projet_nom, p.categorie, e.annee
                FROM rapports r
                JOIN projets p ON r.id_projet = p.id
                LEFT JOIN editions e ON p.id_edition = e.id
                WHERE r.id_projet IN ($placeholders)
                ORDER BY r.date_soumission DESC
            ");
            $stmt->execute($projets_ids);
            $rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $debug_info[] = "Méthode 2 (par projets): " . count($rapports) . " rapports trouvés";
        }
    } catch (Exception $e) {
        $debug_info[] = "Erreur méthode 2: " . $e->getMessage();
    }
}

// Fallback ultime : recherche LIKE dans les membres
if (empty($rapports)) {
    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT r.*, p.nom as projet_nom, p.categorie, e.annee
            FROM rapports r
            JOIN projets p ON r.id_projet = p.id
            LEFT JOIN editions e ON p.id_edition = e.id
            WHERE p.membres LIKE ?
            ORDER BY r.date_soumission DESC
        ");
        $stmt->execute(['%' . $user_nom . '%']);
        $rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $debug_info[] = "Méthode 3 (LIKE): " . count($rapports) . " rapports trouvés";
    } catch (Exception $e) {
        $debug_info[] = "Erreur méthode 3: " . $e->getMessage();
    }
}

// ============================================
// RÉCUPÉRATION DES PRÉSENTATIONS
// ============================================

try {
    $table_exists = $pdo->query("SHOW TABLES LIKE 'presentations'")->rowCount() > 0;
    
    if ($table_exists) {
        // Méthode 1 : Par id_utilisateur
        $has_col = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'id_utilisateur'")->rowCount() > 0;
        if ($has_col) {
            $stmt = $pdo->prepare("
                SELECT pr.*, p.nom as projet_nom, p.categorie, e.annee
                FROM presentations pr
                JOIN projets p ON pr.id_projet = p.id
                LEFT JOIN editions e ON p.id_edition = e.id
                WHERE pr.id_utilisateur = ?
                ORDER BY pr.date_soumission DESC
            ");
            $stmt->execute([$user_id]);
            $presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Si aucune trouvée, méthode par projets
        if (empty($presentations)) {
            $all_projets = $pdo->query("SELECT id, membres FROM projets")->fetchAll(PDO::FETCH_ASSOC);
            $projets_ids = [];
            
            foreach ($all_projets as $projet) {
                if (empty($projet['membres'])) continue;
                $membres = str_replace(["\r\n", "\n", "\r"], ',', $projet['membres']);
                $membres_list = array_map('trim', explode(',', $membres));
                foreach ($membres_list as $membre) {
                    if (strcasecmp(trim($membre), $user_nom) === 0) {
                        $projets_ids[] = $projet['id'];
                        break;
                    }
                }
            }
            
            if (!empty($projets_ids)) {
                $placeholders = str_repeat('?,', count($projets_ids) - 1) . '?';
                $stmt = $pdo->prepare("
                    SELECT pr.*, p.nom as projet_nom, p.categorie, e.annee
                    FROM presentations pr
                    JOIN projets p ON pr.id_projet = p.id
                    LEFT JOIN editions e ON p.id_edition = e.id
                    WHERE pr.id_projet IN ($placeholders)
                    ORDER BY pr.date_soumission DESC
                ");
                $stmt->execute($projets_ids);
                $presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }
} catch (Exception $e) {
    // Ignorer les erreurs pour les présentations
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

// S'assurer que les variables sont définies même si vides
if (!isset($rapports)) $rapports = [];
if (!isset($presentations)) $presentations = [];
if (!isset($debug_info)) $debug_info = [];
?>
