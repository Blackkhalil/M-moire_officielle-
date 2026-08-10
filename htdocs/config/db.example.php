<?php
/**
 * Configuration base de données — modèle à copier vers db.php
 *
 * cp config/db.example.php config/db.php
 * Puis éditez ENV et les identifiants MySQL de l'hébergeur.
 */
if (defined('DB_CONFIG_LOADED')) {
    return;
}
define('DB_CONFIG_LOADED', true);

/** @var 'local'|'production' */
define('ENV', 'local');

if (ENV === 'local') {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'seic_lbs');
} else {
    // Renseigner depuis le panneau MySQL de l'hébergeur (host souvent "localhost"
    // en mutualisé classique, ou une adresse distante type "sqlXXX.hebergeur.com"
    // sur certaines offres gratuites).
    define('DB_HOST', 'localhost');
    define('DB_USER', 'CHANGE_ME');
    define('DB_PASS', 'CHANGE_ME');
    define('DB_NAME', 'CHANGE_ME');
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    if (ENV === 'production') {
        error_log('MySQL connection failed: ' . $conn->connect_error);
        die('Une erreur technique est survenue. Veuillez réessayer plus tard.');
    }
    die('Connexion échouée : ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
