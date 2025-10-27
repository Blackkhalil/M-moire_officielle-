<?php
/**
 * Configuration principale de l'application SEIC LBS
 * 
 * Ce fichier contient toutes les configurations importantes :
 * - Connexion à la base de données MySQL (InfinityFree)
 * - Paramètres de sécurité pour les sessions
 * - Configuration des chemins et URLs
 * - Paramètres d'optimisation pour les performances
 * 
 * @author Khalil
 * @version 1.0
 */

// Configuration de la base de données MySQL (hébergement InfinityFree)
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_NAME', 'if0_40166846_seic'); // Remplace XXX par le vrai nom de ta base dans ton cPanel
define('DB_USER', 'if0_40166846');
define('DB_PASS', 'LOS6JUhZJIM');

// Clé secrète JWT (à personnaliser pour la sécurité)
define('JWT_SECRET', 'votre_cle_secrete_unique');

// Dossier des fichiers uploadés
define('UPLOADS_DIR', __DIR__ . '/../uploads/');

// URL de base de ton site (ajuste selon ton projet)
define('BASE_URL', '/public/');

// Connexion PDO optimisée pour InfinityFree
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
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
    die('❌ Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
