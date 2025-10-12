<?php
/**
 * Configuration InfinityFree pour SEIC LBS
 * 
 * Ce fichier contient la configuration spécifique pour InfinityFree
 * Remplacer les valeurs par vos vraies informations de base de données
 */

// Configuration de la base de données InfinityFree
// REMPLACER CES VALEURS PAR VOS VRAIES INFORMATIONS
define('DB_HOST', 'sqlXXX.infinityfree.com'); // Remplacer XXX par votre numéro
define('DB_NAME', 'seic_lbs'); // Nom de votre base de données
define('DB_USER', 'seic_user'); // Nom de votre utilisateur
define('DB_PASS', 'votre_mot_de_passe'); // Votre mot de passe

// Configuration des URLs InfinityFree
// REMPLACER par votre vraie URL
define('BASE_URL', 'https://votre-site.infinityfreeapp.com');
define('PUBLIC_URL', BASE_URL . '/public');

// Configuration des chemins
define('UPLOADS_DIR', __DIR__ . '/../uploads/');

// Configuration de sécurité pour InfinityFree
ini_set('display_errors', 0); // Masquer les erreurs en production
ini_set('log_errors', 1); // Logger les erreurs
ini_set('error_log', __DIR__ . '/../logs/infinityfree_errors.log');

// Configuration des sessions
ini_set('session.cookie_secure', 1); // HTTPS uniquement
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Configuration des limites d'upload (InfinityFree limite à 50MB)
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('max_execution_time', 300);

// Fuseau horaire
date_default_timezone_set('Europe/Paris');

// Connexion PDO optimisée
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
} catch (PDOException $e) {
    // En cas d'erreur, logger et afficher un message générique
    error_log('Erreur de connexion DB InfinityFree: ' . $e->getMessage());
    die('Erreur de connexion à la base de données. Vérifiez la configuration.');
}

// Créer le dossier de logs s'il n'existe pas
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Créer les dossiers d'upload s'ils n'existent pas
$upload_dirs = [
    __DIR__ . '/../uploads/rapports/',
    __DIR__ . '/../uploads/presentations/'
];

foreach ($upload_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        // Créer un fichier index.php pour la sécurité
        file_put_contents($dir . 'index.php', '<?php header("Location: ../../public/login.php"); exit; ?>');
    }
}
?>
