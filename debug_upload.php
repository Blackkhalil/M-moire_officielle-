<?php
// Script de debug pour diagnostiquer les problèmes d'upload PowerPoint
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    header('Location: ../public/login.php');
    exit;
}

echo "<h2>🔍 Debug Upload PowerPoint</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    
    echo "<h3>📋 Informations du fichier :</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Propriété</th><th>Valeur</th></tr>";
    echo "<tr><td>Nom original</td><td>" . htmlspecialchars($file['name']) . "</td></tr>";
    echo "<tr><td>Taille</td><td>" . number_format($file['size'] / 1024 / 1024, 2) . " MB</td></tr>";
    echo "<tr><td>Type MIME</td><td>" . htmlspecialchars($file['type']) . "</td></tr>";
    echo "<tr><td>Erreur</td><td>" . $file['error'] . "</td></tr>";
    echo "<tr><td>Fichier temporaire</td><td>" . htmlspecialchars($file['tmp_name']) . "</td></tr>";
    echo "</table>";
    
    echo "<h3>🔍 Analyse détaillée :</h3>";
    
    // Vérifier l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    echo "<p><strong>Extension détectée :</strong> " . htmlspecialchars($extension) . "</p>";
    
    if (in_array($extension, ['ppt', 'pptx'])) {
        echo "<p>✅ Extension valide</p>";
    } else {
        echo "<p>❌ Extension invalide</p>";
    }
    
    // Vérifier le type MIME
    if (function_exists('mime_content_type') && file_exists($file['tmp_name'])) {
        $mime_type = mime_content_type($file['tmp_name']);
        echo "<p><strong>Type MIME détecté :</strong> " . htmlspecialchars($mime_type) . "</p>";
        
        $allowed_mimes = [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/msword', // Parfois PowerPoint est détecté comme Word
            'application/octet-stream' // Type générique
        ];
        
        if (in_array($mime_type, $allowed_mimes)) {
            echo "<p>✅ Type MIME valide</p>";
        } else {
            echo "<p>⚠️ Type MIME non reconnu (mais extension valide)</p>";
        }
    } else {
        echo "<p>⚠️ Impossible de détecter le type MIME</p>";
    }
    
    // Vérifier la taille
    if ($file['size'] <= 50 * 1024 * 1024) {
        echo "<p>✅ Taille acceptable</p>";
    } else {
        echo "<p>❌ Fichier trop volumineux</p>";
    }
    
    // Vérifier les erreurs
    $error_messages = [
        UPLOAD_ERR_OK => 'Aucune erreur',
        UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (php.ini)',
        UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (formulaire)',
        UPLOAD_ERR_PARTIAL => 'Upload partiel',
        UPLOAD_ERR_NO_FILE => 'Aucun fichier',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
        UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire',
        UPLOAD_ERR_EXTENSION => 'Extension bloquée'
    ];
    
    echo "<p><strong>Erreur d'upload :</strong> " . ($error_messages[$file['error']] ?? 'Inconnue') . "</p>";
    
    echo "<h3>💡 Recommandation :</h3>";
    if (in_array($extension, ['ppt', 'pptx']) && $file['error'] === UPLOAD_ERR_OK) {
        echo "<div style='background-color: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<strong>✅ Votre fichier devrait être accepté !</strong><br>";
        echo "Le problème vient probablement de la vérification MIME trop stricte. Utilisez maintenant le formulaire normal.";
        echo "</div>";
    } else {
        echo "<div style='background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>❌ Problème détecté :</strong><br>";
        echo "Vérifiez l'erreur ci-dessus et corrigez le problème.";
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Debug Upload PowerPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>🧪 Test d'upload</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="test_file" class="form-label">Sélectionnez un fichier PowerPoint pour test :</label>
            <input type="file" class="form-control" id="test_file" name="test_file" accept=".ppt,.pptx">
        </div>
        <button type="submit" class="btn btn-primary">Analyser le fichier</button>
    </form>
    
    <div class="mt-4">
        <a href="soumettre_powerpoint.php" class="btn btn-success">Retour au formulaire de soumission</a>
    </div>
</div>
</body>
</html>
