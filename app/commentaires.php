<?php
/**
 * Gestion des commentaires des visiteurs sur les projets
 */

// Ne pas vérifier l'authentification ici car ce fichier est inclus depuis visitor_projects.php
// qui fait déjà la vérification

if (!isset($success)) $success = '';
if (!isset($error)) $error = '';

if (is_logged_in()) {
    $user = current_user();
    $user_id = $user['id'];
    $user_role = $user['role'];
} else {
    $user_id = null;
    $user_role = null;
}

// Vérifier si la table commentaires existe
try {
    $table_exists = $pdo->query("SHOW TABLES LIKE 'commentaires'")->rowCount() > 0;
    if (!$table_exists) {
        // Créer la table si elle n'existe pas
        $pdo->exec("
            CREATE TABLE commentaires (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_projet INT NOT NULL,
                id_utilisateur INT NOT NULL,
                commentaire TEXT NOT NULL,
                date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE,
                FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
                INDEX idx_commentaires_projet (id_projet),
                INDEX idx_commentaires_utilisateur (id_utilisateur),
                INDEX idx_commentaires_date (date_creation)
            )
        ");
    }
} catch (Exception $e) {
    // Table existe peut-être déjà
}

// Traitement des actions (seulement si utilisateur connecté)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in() && isset($user_id)) {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'ajouter') {
        $id_projet = (int)($_POST['id_projet'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');
        
        if ($id_projet <= 0) {
            $error = 'Projet invalide.';
        } elseif (empty($commentaire)) {
            $error = 'Le commentaire ne peut pas être vide.';
        } elseif (strlen($commentaire) > 1000) {
            $error = 'Le commentaire est trop long (maximum 1000 caractères).';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO commentaires (id_projet, id_utilisateur, commentaire) VALUES (?, ?, ?)');
                if ($stmt->execute([$id_projet, $user_id, $commentaire])) {
                    $success = 'Commentaire ajouté avec succès !';
                } else {
                    $error = 'Erreur lors de l\'ajout du commentaire.';
                }
            } catch (Exception $e) {
                $error = 'Erreur : ' . $e->getMessage();
            }
        }
    } elseif ($action === 'supprimer') {
        $commentaire_id = (int)($_POST['commentaire_id'] ?? 0);
        
        if ($commentaire_id <= 0) {
            $error = 'Commentaire invalide.';
        } else {
            try {
                // Vérifier que le commentaire appartient à l'utilisateur
                $stmt = $pdo->prepare('SELECT id FROM commentaires WHERE id = ? AND id_utilisateur = ?');
                $stmt->execute([$commentaire_id, $user_id]);
                
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare('DELETE FROM commentaires WHERE id = ?');
                    if ($stmt->execute([$commentaire_id])) {
                        $success = 'Commentaire supprimé avec succès !';
                    } else {
                        $error = 'Erreur lors de la suppression.';
                    }
                } else {
                    $error = 'Vous n\'avez pas le droit de supprimer ce commentaire.';
                }
            } catch (Exception $e) {
                $error = 'Erreur : ' . $e->getMessage();
            }
        }
    }
}

// Fonction pour récupérer les commentaires d'un projet
function getCommentairesProjet($pdo, $id_projet) {
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, u.nom as nom_utilisateur, u.email as email_utilisateur
            FROM commentaires c
            JOIN utilisateurs u ON c.id_utilisateur = u.id
            WHERE c.id_projet = ?
            ORDER BY c.date_creation DESC
        ");
        $stmt->execute([$id_projet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Fonction pour compter les commentaires d'un projet
function countCommentairesProjet($pdo, $id_projet) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM commentaires WHERE id_projet = ?");
        $stmt->execute([$id_projet]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

?>
