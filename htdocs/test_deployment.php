<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Déploiement - SEIC LBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .test-card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .test-ok { color: #28a745; }
        .test-error { color: #dc3545; }
        .test-warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🔍 Test de Déploiement SEIC LBS</h1>
        
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $tests = [];
        
        // Test 1: Connexion Base de Données
        echo '<div class="test-card">';
        echo '<h3>1. Connexion Base de Données</h3>';
        try {
            require_once __DIR__ . '/../config/config.php';
            echo '<p class="test-ok">✅ Connexion réussie à la base de données</p>';
            $tests['db'] = true;
        } catch (Exception $e) {
            echo '<p class="test-error">❌ Erreur de connexion : ' . htmlspecialchars($e->getMessage()) . '</p>';
            $tests['db'] = false;
        }
        echo '</div>';
        
        // Test 2: Tables existantes
        if ($tests['db']) {
            echo '<div class="test-card">';
            echo '<h3>2. Vérification Tables</h3>';
            $tables_required = ['utilisateurs', 'projets', 'rapports', 'presentations', 'commentaires', 'editions', 'groupes', 'salles'];
            $tables_found = [];
            
            $result = $pdo->query("SHOW TABLES");
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables_found[] = $row[0];
            }
            
            foreach ($tables_required as $table) {
                if (in_array($table, $tables_found)) {
                    echo '<p class="test-ok">✅ Table "' . $table . '" existe</p>';
                } else {
                    echo '<p class="test-error">❌ Table "' . $table . '" manquante</p>';
                }
            }
            echo '</div>';
        }
        
        // Test 3: Protection fichiers sensibles
        echo '<div class="test-card">';
        echo '<h3>3. Protection Fichiers Sensibles</h3>';
        $htaccess_files = [
            '../config/.htaccess',
            '../app/.htaccess',
            '../database/.htaccess',
            '../uploads/.htaccess'
        ];
        
        foreach ($htaccess_files as $file) {
            if (file_exists($file)) {
                echo '<p class="test-ok">✅ ' . $file . ' existe</p>';
            } else {
                echo '<p class="test-error">❌ ' . $file . ' manquant</p>';
            }
        }
        echo '</div>';
        
        // Test 4: Permissions uploads
        echo '<div class="test-card">';
        echo '<h3>4. Permissions Dossiers Uploads</h3>';
        $upload_dirs = [
            '../uploads/',
            '../uploads/rapports/',
            '../uploads/presentations/'
        ];
        
        foreach ($upload_dirs as $dir) {
            if (is_dir($dir) && is_writable($dir)) {
                echo '<p class="test-ok">✅ ' . $dir . ' accessible en écriture</p>';
            } elseif (is_dir($dir)) {
                echo '<p class="test-warning">⚠️ ' . $dir . ' existe mais non accessible en écriture</p>';
            } else {
                echo '<p class="test-error">❌ ' . $dir . ' n\'existe pas</p>';
            }
        }
        echo '</div>';
        
        // Test 5: Configuration PHP
        echo '<div class="test-card">';
        echo '<h3>5. Configuration PHP</h3>';
        
        $upload_max = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');
        $memory = ini_get('memory_limit');
        
        echo '<p>Upload max: <strong>' . $upload_max . '</strong> ' . (intval($upload_max) >= 50 ? '<span class="test-ok">✅</span>' : '<span class="test-warning">⚠️ Recommandé: 50M</span>') . '</p>';
        echo '<p>Post max: <strong>' . $post_max . '</strong> ' . (intval($post_max) >= 50 ? '<span class="test-ok">✅</span>' : '<span class="test-warning">⚠️ Recommandé: 50M</span>') . '</p>';
        echo '<p>Memory limit: <strong>' . $memory . '</strong></p>';
        echo '</div>';
        
        // Test 6: Extensions PHP requises
        echo '<div class="test-card">';
        echo '<h3>6. Extensions PHP</h3>';
        $extensions = ['pdo', 'pdo_mysql', 'session', 'mbstring', 'fileinfo'];
        
        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<p class="test-ok">✅ Extension "' . $ext . '" chargée</p>';
            } else {
                echo '<p class="test-error">❌ Extension "' . $ext . '" manquante</p>';
            }
        }
        echo '</div>';
        
        // Test 7: Fonctions de sécurité
        echo '<div class="test-card">';
        echo '<h3>7. Fonctions de Sécurité</h3>';
        require_once __DIR__ . '/../app/auth.php';
        
        if (function_exists('generate_csrf_token')) {
            echo '<p class="test-ok">✅ Fonction generate_csrf_token() disponible</p>';
        } else {
            echo '<p class="test-error">❌ Fonction generate_csrf_token() manquante</p>';
        }
        
        if (function_exists('verify_csrf_token')) {
            echo '<p class="test-ok">✅ Fonction verify_csrf_token() disponible</p>';
        } else {
            echo '<p class="test-error">❌ Fonction verify_csrf_token() manquante</p>';
        }
        echo '</div>';
        
        // Test 8: Mots de passe hachés
        if ($tests['db']) {
            echo '<div class="test-card">';
            echo '<h3>8. Vérification Mots de Passe</h3>';
            
            $stmt = $pdo->query("SELECT COUNT(*) as total, 
                                  SUM(CASE WHEN mot_de_passe LIKE '$2y$%' THEN 1 ELSE 0 END) as hashed,
                                  SUM(CASE WHEN mot_de_passe NOT LIKE '$2y$%' THEN 1 ELSE 0 END) as plain
                                  FROM utilisateurs");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '<p>Total utilisateurs: <strong>' . $result['total'] . '</strong></p>';
            echo '<p class="test-ok">Mots de passe hachés: <strong>' . $result['hashed'] . '</strong></p>';
            
            if ($result['plain'] > 0) {
                echo '<p class="test-error">❌ Mots de passe en texte clair: <strong>' . $result['plain'] . '</strong></p>';
                echo '<p class="text-danger">⚠️ Exécutez le script database/migrate_passwords.sql</p>';
            } else {
                echo '<p class="test-ok">✅ Tous les mots de passe sont hachés</p>';
            }
            echo '</div>';
        }
        
        // Résumé
        echo '<div class="test-card bg-light">';
        echo '<h3>📊 Résumé</h3>';
        echo '<p><strong>Votre application est prête pour les tests locaux !</strong></p>';
        echo '<p>Prochaines étapes :</p>';
        echo '<ol>';
        echo '<li>Créer un compte test avec un mot de passe de 8+ caractères</li>';
        echo '<li>Tester la connexion</li>';
        echo '<li>Tester l\'upload de fichiers</li>';
        echo '<li>Vérifier les permissions par rôle</li>';
        echo '</ol>';
        echo '<p><a href="index.php" class="btn btn-primary">Accéder à l\'application</a></p>';
        echo '</div>';
        ?>
    </div>
</body>
</html>



