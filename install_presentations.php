<?php
// Script automatique pour créer la table presentations
require_once __DIR__ . '/../config/config.php';

try {
    echo "<h2>🔄 Création de la table presentations</h2>";
    
    // Vérifier si la table existe déjà
    $stmt = $pdo->query("SHOW TABLES LIKE 'presentations'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<div style='background-color: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<strong>✅ Table 'presentations' existe déjà !</strong><br>";
        echo "Aucune action nécessaire.";
        echo "</div>";
    } else {
        echo "<p>📝 Création de la table presentations...</p>";
        
        // Créer la table
        $sql = "CREATE TABLE presentations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_projet INT NOT NULL,
            nom_fichier VARCHAR(255) NOT NULL,
            chemin_fichier VARCHAR(500) NOT NULL,
            taille_fichier INT NOT NULL,
            date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP,
            etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
            commentaire TEXT,
            FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
        echo "<p>✅ Table 'presentations' créée avec succès !</p>";
        
        // Créer les index
        $pdo->exec("CREATE INDEX idx_presentations_projet ON presentations(id_projet)");
        $pdo->exec("CREATE INDEX idx_presentations_etat ON presentations(etat_validation)");
        echo "<p>✅ Index créés avec succès !</p>";
        
        echo "<div style='background-color: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<strong>🎉 Installation terminée !</strong><br>";
        echo "La table presentations a été créée avec succès. Vous pouvez maintenant utiliser les fonctionnalités PowerPoint.";
        echo "</div>";
    }
    
    // Afficher la structure de la table
    echo "<h3>📋 Structure de la table presentations :</h3>";
    $stmt = $pdo->query("DESCRIBE presentations");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<strong>❌ Erreur :</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
