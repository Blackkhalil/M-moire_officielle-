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
 * @version 1.0
 */

// Configuration de la base de données MySQL

define('DB_HOST', 'localhost');
define('DB_NAME', 'seic_lbs');
define('DB_USER', 'root');
define('DB_PASS', '');

define('JWT_SECRET', 'votre_cle_secrete'); // À personnaliser

define('UPLOADS_DIR', __DIR__ . '/../uploads/');

define('BASE_URL', '/public/');

// Connexion PDO optimisée pour multi-utilisateurs
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_PERSISTENT => false, // Évite les connexions persistantes pour éviter les conflits
        PDO::ATTR_TIMEOUT => 30, // Timeout de 30 secondes
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true // Optimise les requêtes
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
} 