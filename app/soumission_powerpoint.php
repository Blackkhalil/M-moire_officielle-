<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/upload_config.php';

$success = '';
$error = '';

// Récupérer les projets de l'étudiant connecté
$user_id = current_user()['id'];
$projets = $pdo->query('SELECT * FROM projets ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_projet_raw = $_POST['id_projet'] ?? null;
    
    // Valider explicitement la sélection du projet (évite le cas id=0 considéré comme falsy)
    if ($id_projet_raw === null || $id_projet_raw === '') {
        $error = 'Veuillez sélectionner un projet.';
    } elseif (!ctype_digit((string)$id_projet_raw)) {
        $error = 'Projet invalide.';
    } elseif (!isset($_FILES['presentation'])) {
        $error = 'Aucun fichier n\'a été sélectionné.';
    } elseif ($_FILES['presentation']['error'] !== UPLOAD_ERR_OK) {
        // Messages d'erreur détaillés
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'Le fichier est trop volumineux (limite PHP: ' . ini_get('upload_max_filesize') . ', POST: ' . ini_get('post_max_size') . ').',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier est trop volumineux (limite formulaire: ' . ini_get('post_max_size') . ').',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque.',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement.'
        ];
        
        $error_code = $_FILES['presentation']['error'];
        $error = $error_messages[$error_code] ?? 'Erreur inconnue lors du téléchargement.';
    } else {
        // Vérification d'existence du projet en base
        $id_projet = (int) $id_projet_raw;
        $stmtProjet = $pdo->prepare('SELECT id FROM projets WHERE id = ?');
        $stmtProjet->execute([$id_projet]);
        if (!$stmtProjet->fetchColumn()) {
            $error = 'Projet introuvable.';
        }
    }

    if (!$error) {
        $file = $_FILES['presentation'];
        
        // Vérifier l'extension du fichier
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['ppt', 'pptx'];
        
        if (!in_array($extension, $allowed_extensions)) {
            $error = 'Seuls les fichiers PowerPoint (.ppt et .pptx) sont autorisés.';
        } elseif ($file['size'] > 50 * 1024 * 1024) { // 50MB max
            $error = 'Le fichier ne doit pas dépasser 50MB.';
        } elseif ($file['size'] == 0) {
            $error = 'Le fichier est vide ou corrompu.';
        } else {
            // Vérification supplémentaire du type MIME (optionnelle)
            $allowed_mimes = [
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/msword', // Parfois PowerPoint est détecté comme Word
                'application/octet-stream' // Type générique
            ];
            
            $mime_type = $file['type'];
            if (!empty($mime_type) && !in_array($mime_type, $allowed_mimes)) {
                // Ne pas bloquer si le MIME n'est pas reconnu, mais logger pour debug
                error_log("Type MIME non reconnu pour PowerPoint: " . $mime_type . " (fichier: " . $file['name'] . ")");
            }
            // Créer le dossier d'upload s'il n'existe pas
            $upload_dir = __DIR__ . '/../uploads/presentations/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Générer un nom de fichier unique
            $filename = 'presentation_' . $id_projet . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Enregistrer en base de données avec l'ID de l'utilisateur
                // Vérifier si la colonne id_utilisateur existe
                $columns_check = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'id_utilisateur'")->rowCount();
                if ($columns_check > 0) {
                    $stmt = $pdo->prepare('INSERT INTO presentations (id_projet, id_utilisateur, nom_fichier, chemin_fichier, taille_fichier) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$id_projet, $user_id, $file['name'], $filename, $file['size']]);
                } else {
                    // Fallback si la colonne n'existe pas encore
                    $stmt = $pdo->prepare('INSERT INTO presentations (id_projet, nom_fichier, chemin_fichier, taille_fichier) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$id_projet, $file['name'], $filename, $file['size']]);
                }
                
                if ($stmt->rowCount() > 0) {
                    $success = 'Présentation soumise avec succès !';
                } else {
                    $error = 'Erreur lors de l\'enregistrement en base de données.';
                    unlink($filepath); // Supprimer le fichier en cas d'erreur
                }
            } else {
                $error = 'Erreur lors du téléchargement du fichier.';
            }
        }
    }
}
?>