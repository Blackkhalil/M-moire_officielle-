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

// Configuration environnement
define('APP_ENV', 'development'); // Changer en 'production' pour le déploiement

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
define('DB_HOST', 'localhost');
define('DB_NAME', 'seic_lbs');
define('DB_USER', 'root');
define('DB_PASS', ''); // À configurer avec un mot de passe fort en production

// Clé secrète JWT (GÉNÉRÉE AUTOMATIQUEMENT - NE PAS PARTAGER)
define('JWT_SECRET', 'f611c6ade42fbd889d683b90735e1ea69dce8069a09877fb74e874d1c346b872');

// Dossier des fichiers uploadés
define('UPLOADS_DIR', __DIR__ . '/../uploads/');

// URL de base du site
define('BASE_URL', '/M-moire_officielle-/htdocs/');

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
?>
