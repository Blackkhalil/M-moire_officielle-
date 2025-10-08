<?php
// Script de mise à jour pour ajouter la table presentations
require_once __DIR__ . '/../config/config.php';

try {
    echo "🔄 Ajout de la table presentations...\n";
    
    // Vérifier si la table existe déjà
    $stmt = $pdo->query("SHOW TABLES LIKE 'presentations'");
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        // Créer la table presentations
        $sql = "
        CREATE TABLE presentations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_projet INT NOT NULL,
            nom_fichier VARCHAR(255) NOT NULL,
            chemin_fichier VARCHAR(500) NOT NULL,
            taille_fichier INT,
            type_mime VARCHAR(100),
            date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP,
            etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
            commentaire TEXT,
            FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
        echo "✅ Table 'presentations' créée avec succès\n";
        
        // Ajouter les index
        $pdo->exec("CREATE INDEX idx_presentations_projet ON presentations(id_projet)");
        $pdo->exec("CREATE INDEX idx_presentations_etat ON presentations(etat_validation)");
        echo "✅ Index ajoutés\n";
        
    } else {
        echo "ℹ️ Table 'presentations' existe déjà\n";
    }
    
    echo "\n🎉 Mise à jour terminée avec succès !\n";
    echo "Vous pouvez maintenant utiliser la gestion des présentations PowerPoint.\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur lors de la mise à jour : " . $e->getMessage() . "\n";
}
?>
