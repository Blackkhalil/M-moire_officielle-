<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$success = '';
$error = '';

// Récupérer les projets de l'étudiant
$user_id = current_user()['id'];
$projets = $pdo->prepare('
    SELECT p.*, e.annee 
    FROM projets p 
    JOIN editions e ON p.id_edition = e.id 
    WHERE p.id_edition IN (
        SELECT id FROM editions ORDER BY annee DESC
    )
    ORDER BY e.annee DESC, p.nom
');
$projets->execute();
$projets = $projets->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_projet = $_POST['id_projet'] ?? null;
    
    if ($id_projet && isset($_FILES['presentation']) && $_FILES['presentation']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['presentation'];
        
        // Vérifier le type de fichier
        $allowed_types = [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            $error = 'Seuls les fichiers PowerPoint (.ppt et .pptx) sont autorisés.';
        } else {
            // Créer le dossier uploads/presentations s'il n'existe pas
            $upload_dir = __DIR__ . '/../uploads/presentations/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $unique_name = uniqid() . '_' . time() . '.' . $extension;
            $file_path = $upload_dir . $unique_name;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Enregistrer en base de données
                $stmt = $pdo->prepare('
                    INSERT INTO presentations (id_projet, nom_fichier, chemin_fichier, taille_fichier, type_mime) 
                    VALUES (?, ?, ?, ?, ?)
                ');
                
                if ($stmt->execute([
                    $id_projet,
                    $file['name'],
                    $file_path,
                    $file['size'],
                    $file_type
                ])) {
                    $success = 'Présentation PowerPoint soumise avec succès !';
                } else {
                    $error = 'Erreur lors de l\'enregistrement en base de données.';
                    unlink($file_path); // Supprimer le fichier en cas d'erreur
                }
            } else {
                $error = 'Erreur lors du téléchargement du fichier.';
            }
        }
    } else {
        $error = 'Veuillez sélectionner un projet et un fichier PowerPoint.';
    }
}
?>
