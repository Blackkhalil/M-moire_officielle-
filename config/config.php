<?php
/**
 * Configuration principale de l'application SEIC LBS
 * 
 * Ce fichier contient toutes les configurations importantes :
 * - Connexion à la base de données MySQL
 * - Paramètres de sécurité pour les sessions
 * - Configuration des chemins et URLs
 * - Paramètres d'optimisation pour les performances
 * 
 * @author Khalil
 * @version 2.0 - Production Ready
 */

// Chargement simple des variables d'environnement depuis .env (si présent)
$env_file = __DIR__ . '/../.env';
if (is_file($env_file) && is_readable($env_file)) {
    $env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $env_line) {
        if ($env_line === '' || $env_line[0] === '#') {
            continue;
        }
        $parts = explode('=', $env_line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

function env_value($key, $default = null) {
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

// Configuration environnement
define('APP_ENV', env_value('APP_ENV', 'development'));

// Gestion des erreurs selon l'environnement
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Configuration de la base de données MySQL
define('DB_HOST', env_value('DB_HOST', 'localhost'));
define('DB_NAME', env_value('DB_NAME', 'seic_lbs'));
define('DB_USER', env_value('DB_USER', 'root'));
define('DB_PASS', env_value('DB_PASS', ''));

// Clé secrète JWT (GÉNÉRÉE AUTOMATIQUEMENT - NE PAS PARTAGER)
define('JWT_SECRET', env_value('JWT_SECRET', 'CHANGE_ME_IN_ENV'));

// Dossier des fichiers uploadés
define('UPLOADS_DIR', __DIR__ . '/../uploads/');

// URL de base du site
define('BASE_URL', env_value('BASE_URL', '/M-moire_officielle-/htdocs/'));

// Connexion PDO sécurisée
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 30,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]
    );
} catch (PDOException $e) {
    if (APP_ENV === 'production') {
        error_log('Database connection error: ' . $e->getMessage());
        die('Une erreur technique est survenue. Veuillez réessayer plus tard.');
    } else {
        die('❌ Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}
