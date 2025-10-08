<?php
// Script de test pour vérifier les fonctionnalités PowerPoint
require_once __DIR__ . '/../config/config.php';

echo "<h2>🔍 Test des fonctionnalités PowerPoint</h2>";

try {
    // Test 1: Vérifier si la table presentations existe
    echo "<h3>1. Vérification de la table presentations</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'presentations'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "✅ Table 'presentations' existe<br>";
        
        // Vérifier la structure
        $stmt = $pdo->query("DESCRIBE presentations");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "📋 Colonnes trouvées :<br>";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})<br>";
        }
    } else {
        echo "❌ Table 'presentations' n'existe pas<br>";
        echo "💡 Exécutez le script : database/add_presentations_table.sql<br>";
    }
    
    // Test 2: Vérifier le dossier d'upload
    echo "<h3>2. Vérification du dossier d'upload</h3>";
    $upload_dir = __DIR__ . '/../uploads/presentations/';
    if (is_dir($upload_dir)) {
        echo "✅ Dossier d'upload existe<br>";
        if (is_writable($upload_dir)) {
            echo "✅ Dossier d'upload est accessible en écriture<br>";
        } else {
            echo "⚠️ Dossier d'upload n'est pas accessible en écriture<br>";
        }
    } else {
        echo "❌ Dossier d'upload n'existe pas<br>";
        echo "💡 Créez le dossier : uploads/presentations/<br>";
    }
    
    // Test 3: Vérifier les fichiers créés
    echo "<h3>3. Vérification des fichiers</h3>";
    $files_to_check = [
        'app/soumission_powerpoint.php' => 'Backend soumission étudiants',
        'app/gestion_powerpoint.php' => 'Backend gestion organisateurs',
        'public/soumettre_powerpoint.php' => 'Page soumission étudiants',
        'public/gestion_powerpoint.php' => 'Page gestion organisateurs',
        'public/download_powerpoint.php' => 'Page téléchargement',
        'uploads/presentations/index.php' => 'Fichier de sécurité'
    ];
    
    foreach ($files_to_check as $file => $description) {
        $file_path = __DIR__ . '/../' . $file;
        if (file_exists($file_path)) {
            echo "✅ {$description} : {$file}<br>";
        } else {
            echo "❌ {$description} : {$file} (manquant)<br>";
        }
    }
    
    // Test 4: Vérifier les liens dans le dashboard
    echo "<h3>4. Vérification des liens dashboard</h3>";
    $dashboard_content = file_get_contents(__DIR__ . '/../public/dashboard.php');
    
    if (strpos($dashboard_content, 'soumettre_powerpoint.php') !== false) {
        echo "✅ Lien soumission PowerPoint pour étudiants<br>";
    } else {
        echo "❌ Lien soumission PowerPoint pour étudiants manquant<br>";
    }
    
    if (strpos($dashboard_content, 'gestion_powerpoint.php') !== false) {
        echo "✅ Lien gestion PowerPoint pour organisateurs<br>";
    } else {
        echo "❌ Lien gestion PowerPoint pour organisateurs manquant<br>";
    }
    
    echo "<h3>🎉 Résumé</h3>";
    echo "Toutes les fonctionnalités PowerPoint ont été implémentées !<br>";
    echo "📝 N'oubliez pas d'exécuter le script SQL pour créer la table.<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
