<?php
/**
 * Script de mise à jour automatique de la base de données
 * Ajoute les colonnes id_utilisateur si elles n'existent pas
 * 
 * À exécuter une seule fois via le navigateur ou en ligne de commande
 * URL: http://localhost/M-moire_officielle-/database/update_database.php
 */

require_once __DIR__ . '/../config/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mise à jour de la base de données</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; margin: 10px 0; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>🔧 Mise à jour de la base de données SEIC</h1>
    
<?php
$errors = [];
$success = [];

try {
    // Vérifier la connexion
    if (!$pdo) {
        throw new Exception("Impossible de se connecter à la base de données");
    }
    
    echo '<div class="info">✅ Connexion à la base de données réussie</div>';
    
    // 1. Ajouter id_utilisateur à la table rapports
    try {
        $check = $pdo->query("SHOW COLUMNS FROM rapports LIKE 'id_utilisateur'")->rowCount();
        if ($check == 0) {
            $pdo->exec("ALTER TABLE rapports ADD COLUMN id_utilisateur INT NULL AFTER id_projet");
            $pdo->exec("ALTER TABLE rapports ADD FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL");
            $pdo->exec("CREATE INDEX idx_rapports_utilisateur ON rapports(id_utilisateur)");
            $success[] = "Colonne id_utilisateur ajoutée à la table rapports";
        } else {
            $success[] = "Colonne id_utilisateur existe déjà dans rapports";
        }
    } catch (Exception $e) {
        $errors[] = "Erreur avec rapports: " . $e->getMessage();
    }
    
    // 2. Ajouter id_utilisateur à la table presentations
    try {
        $table_exists = $pdo->query("SHOW TABLES LIKE 'presentations'")->rowCount() > 0;
        if ($table_exists) {
            $check = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'id_utilisateur'")->rowCount();
            if ($check == 0) {
                $pdo->exec("ALTER TABLE presentations ADD COLUMN id_utilisateur INT NULL AFTER id_projet");
                $pdo->exec("ALTER TABLE presentations ADD FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL");
                $pdo->exec("CREATE INDEX idx_presentations_utilisateur ON presentations(id_utilisateur)");
                $success[] = "Colonne id_utilisateur ajoutée à la table presentations";
            } else {
                $success[] = "Colonne id_utilisateur existe déjà dans presentations";
            }
        } else {
            $success[] = "Table presentations n'existe pas encore (sera créée lors de la première soumission)";
        }
    } catch (Exception $e) {
        $errors[] = "Erreur avec presentations: " . $e->getMessage();
    }
    
    // 3. Ajouter id_utilisateur à la table projets
    try {
        $check = $pdo->query("SHOW COLUMNS FROM projets LIKE 'id_utilisateur'")->rowCount();
        if ($check == 0) {
            $pdo->exec("ALTER TABLE projets ADD COLUMN id_utilisateur INT NULL AFTER id");
            $pdo->exec("ALTER TABLE projets ADD FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL");
            $pdo->exec("CREATE INDEX idx_projets_utilisateur ON projets(id_utilisateur)");
            $success[] = "Colonne id_utilisateur ajoutée à la table projets";
        } else {
            $success[] = "Colonne id_utilisateur existe déjà dans projets";
        }
    } catch (Exception $e) {
        $errors[] = "Erreur avec projets: " . $e->getMessage();
    }
    
    // 4. Créer la table commentaires si elle n'existe pas
    try {
        $table_exists = $pdo->query("SHOW TABLES LIKE 'commentaires'")->rowCount() > 0;
        if (!$table_exists) {
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
            $success[] = "Table commentaires créée avec succès";
        } else {
            $success[] = "Table commentaires existe déjà";
        }
    } catch (Exception $e) {
        $errors[] = "Erreur avec commentaires: " . $e->getMessage();
    }
    
    // Afficher les résultats
    foreach ($success as $msg) {
        echo '<div class="success">✅ ' . htmlspecialchars($msg) . '</div>';
    }
    
    foreach ($errors as $msg) {
        echo '<div class="error">❌ ' . htmlspecialchars($msg) . '</div>';
    }
    
    if (empty($errors)) {
        echo '<div class="success"><strong>🎉 Mise à jour terminée avec succès !</strong></div>';
        echo '<div class="info">Vous pouvez maintenant fermer cette page. Le système fonctionnera correctement.</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="error"><strong>❌ Erreur fatale:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

</body>
</html>
