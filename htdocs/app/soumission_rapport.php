<?php
/**
 * Gestion de la soumission des rapports par les étudiants
 * 
 * Ce fichier gère tout le processus de soumission d'un rapport PDF :
 * - Vérification que l'utilisateur est bien un étudiant
 * - Récupération de la liste des projets disponibles
 * - Validation du fichier PDF (taille, format, etc.)
 * - Upload sécurisé dans le dossier uploads/rapports/
 * - Enregistrement en base de données avec état "en_attente"
 * 
 * @author Khalil
 * @version 1.0
 */

// On vérifie d'abord que l'utilisateur est connecté et qu'il est étudiant
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    redirect_to('login.php');
}

// On charge la configuration de la base de données et les paramètres d'upload
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/upload_config.php';

require_once __DIR__ . '/mailer.php';

function notify_organizers_of_rapport_submission(PDO $pdo, int $id_projet): void
{
    $stmt = $pdo->prepare('SELECT nom FROM projets WHERE id = ?');
    $stmt->execute([$id_projet]);
    $projetNom = (string) ($stmt->fetchColumn() ?: 'Projet #' . $id_projet);

    send_submission_notification_to_organizers(
        $pdo,
        'rapport',
        (string) (current_user()['nom'] ?? ''),
        $projetNom,
        url_to('rapports_admin.php')
    );
}

// Variables pour gérer les messages de succès et d'erreur
$success = '';
$error = '';

// On récupère tous les projets disponibles pour que l'étudiant puisse choisir
$user_id = current_user()['id'];
$projets = $pdo->query('SELECT * FROM projets ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);

// Si le formulaire a été soumis, on traite la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $id_projet_raw = $_POST['id_projet'] ?? null;
    
    // On valide d'abord la sélection du projet (important pour éviter les erreurs)
    if ($id_projet_raw === null || $id_projet_raw === '') {
        $error = 'Veuillez sélectionner un projet.';
    } elseif (!ctype_digit((string)$id_projet_raw)) {
        $error = 'Projet invalide.';
    } elseif (!isset($_FILES['rapport'])) {
        $error = 'Aucun fichier n\'a été sélectionné.';
    } elseif ($_FILES['rapport']['error'] !== UPLOAD_ERR_OK) {
        // Messages d'erreur détaillés pour aider l'utilisateur
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'Le fichier est trop volumineux (limite PHP: ' . ini_get('upload_max_filesize') . ', POST: ' . ini_get('post_max_size') . ').',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier est trop volumineux (limite formulaire: ' . ini_get('post_max_size') . ').',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque.',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement.'
        ];
        
        $error_code = $_FILES['rapport']['error'];
        $error = $error_messages[$error_code] ?? 'Erreur inconnue lors du téléchargement.';
    } else {
        // On vérifie que le projet existe vraiment en base (sécurité)
        $id_projet = (int) $id_projet_raw;
        $stmtProjet = $pdo->prepare('SELECT id FROM projets WHERE id = ?');
        $stmtProjet->execute([$id_projet]);
        if (!$stmtProjet->fetchColumn()) {
            $error = 'Projet introuvable.';
        }
    }

    // Si pas d'erreur jusqu'ici, on traite le fichier
    if (!$error) {
        $file = $_FILES['rapport'];
        
        // On vérifie que c'est bien un PDF
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf'];
        
        if (!in_array($extension, $allowed_extensions)) {
            $error = 'Seuls les fichiers PDF (.pdf) sont autorisés.';
        } elseif ($file['size'] > 50 * 1024 * 1024) { // Limite à 50MB
            $error = 'Le fichier ne doit pas dépasser 50MB.';
        } elseif ($file['size'] == 0) {
            $error = 'Le fichier est vide ou corrompu.';
        } else {
            // Vérification optionnelle du type MIME (pas toujours fiable)
            $allowed_mimes = [
                'application/pdf',
                'application/octet-stream' // Type générique
            ];
            
            $mime_type = $file['type'];
            if (!empty($mime_type) && !in_array($mime_type, $allowed_mimes)) {
                // On log pour debug mais on ne bloque pas
                error_log("Type MIME non reconnu pour PDF: " . $mime_type . " (fichier: " . $file['name'] . ")");
            }
            
            // On crée le dossier d'upload s'il n'existe pas
            $upload_dir = rtrim(UPLOADS_DIR, '/\\') . DIRECTORY_SEPARATOR . 'rapports' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // On génère un nom de fichier unique pour éviter les conflits
            $filename = 'rapport_' . $id_projet . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            // On déplace le fichier uploadé vers notre dossier
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // On enregistre en base de données avec l'état "en_attente" et l'ID de l'utilisateur
                try {
                    // Vérifier si la colonne id_utilisateur existe
                    $columns_check = $pdo->query("SHOW COLUMNS FROM rapports LIKE 'id_utilisateur'")->rowCount();
                    if ($columns_check > 0) {
                        $stmt = $pdo->prepare('INSERT INTO rapports (id_projet, id_utilisateur, date_soumission, fichier, etat_validation) VALUES (?, ?, NOW(), ?, "en_attente")');
                        $result = $stmt->execute([$id_projet, $user_id, $filename]);
                    } else {
                        // Fallback si la colonne n'existe pas encore
                        $stmt = $pdo->prepare('INSERT INTO rapports (id_projet, date_soumission, fichier, etat_validation) VALUES (?, NOW(), ?, "en_attente")');
                        $result = $stmt->execute([$id_projet, $filename]);
                    }
                    
                    if ($result && $stmt->rowCount() > 0) {
                        $success = 'Rapport soumis avec succès !';
                        notify_organizers_of_rapport_submission($pdo, $id_projet);
                    } else {
                        $error = 'Erreur lors de l\'enregistrement en base de données.';
                        unlink($filepath); // On supprime le fichier si l'enregistrement échoue
                    }
                } catch (Exception $e) {
                    // En cas d'erreur, essayer sans id_utilisateur
                    try {
                        $stmt = $pdo->prepare('INSERT INTO rapports (id_projet, date_soumission, fichier, etat_validation) VALUES (?, NOW(), ?, "en_attente")');
                        if ($stmt->execute([$id_projet, $filename])) {
                            $success = 'Rapport soumis avec succès !';
                            notify_organizers_of_rapport_submission($pdo, $id_projet);
                        } else {
                            $error = 'Erreur lors de l\'enregistrement en base de données.';
                            unlink($filepath);
                        }
                    } catch (Exception $e2) {
                        $error = 'Erreur lors de l\'enregistrement : ' . $e2->getMessage();
                        unlink($filepath);
                    }
                }
            } else {
                $error = 'Erreur lors du téléchargement du fichier.';
            }
        }
    }
}
?> 