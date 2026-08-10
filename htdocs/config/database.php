<?php
// Compat : centralise la connexion PDO pour les scripts qui attendent config/database.php
require_once __DIR__ . '/config.php';

// $pdo est défini dans config.php
if (!isset($pdo) || !$pdo instanceof PDO) {
    throw new RuntimeException('Connexion PDO indisponible.');
}

