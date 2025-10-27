<?php
/**
 * Script d'installation des optimisations multi-utilisateurs
 * À exécuter une seule fois pour créer les tables nécessaires
 */

require_once __DIR__ . '/../config/config.php';

echo "Installation des optimisations multi-utilisateurs...\n";

try {
    // Lire le fichier SQL
    $sql = file_get_contents(__DIR__ . '/optimization_tables.sql');
    
    if ($sql === false) {
        throw new Exception("Impossible de lire le fichier optimization_tables.sql");
    }
    
    // Diviser les requêtes
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($queries as $query) {
        if (empty($query)) continue;
        
        try {
            $pdo->exec($query);
            $successCount++;
            echo "✓ Requête exécutée avec succès\n";
        } catch (PDOException $e) {
            $errorCount++;
            echo "✗ Erreur: " . $e->getMessage() . "\n";
            echo "Requête: " . substr($query, 0, 100) . "...\n";
        }
    }
    
    echo "\n=== Résumé ===\n";
    echo "Requêtes réussies: $successCount\n";
    echo "Erreurs: $errorCount\n";
    
    if ($errorCount === 0) {
        echo "\n🎉 Installation terminée avec succès !\n";
        echo "Le système est maintenant optimisé pour plusieurs utilisateurs simultanés.\n";
    } else {
        echo "\n⚠️  Installation terminée avec des erreurs.\n";
        echo "Vérifiez les messages d'erreur ci-dessus.\n";
    }
    
} catch (Exception $e) {
    echo "Erreur fatale: " . $e->getMessage() . "\n";
    exit(1);
}
?>
