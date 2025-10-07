<?php
// Configuration de la base de données

define('DB_HOST', 'localhost');
define('DB_NAME', 'seic_lbs');
define('DB_USER', 'root');
define('DB_PASS', '');

define('JWT_SECRET', 'votre_cle_secrete'); // À personnaliser

define('UPLOADS_DIR', __DIR__ . '/../uploads/');

define('BASE_URL', '/public/');

// Connexion PDO
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
} 