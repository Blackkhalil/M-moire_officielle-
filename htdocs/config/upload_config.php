<?php
// Configuration PHP pour améliorer les uploads
// Ce fichier peut être inclus au début des scripts d'upload

// Augmenter les limites d'upload si elles sont trop faibles
$current_upload_max = ini_get('upload_max_filesize');
$current_post_max = ini_get('post_max_size');

// Convertir en bytes pour comparaison
function config_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

$upload_bytes = config_bytes($current_upload_max);
$post_bytes = config_bytes($current_post_max);

// Si les limites sont trop faibles, essayer de les augmenter
if ($upload_bytes < 50 * 1024 * 1024) {
    ini_set('upload_max_filesize', '50M');
}

if ($post_bytes < 150 * 1024 * 1024) {
    ini_set('post_max_size', '150M');
}

// Augmenter le temps d'exécution pour les gros fichiers
ini_set('max_execution_time', 300); // 5 minutes

// Augmenter la limite de mémoire
ini_set('memory_limit', '256M');

// Log des configurations pour debug
error_log("Configuration upload - upload_max_filesize: " . ini_get('upload_max_filesize'));
error_log("Configuration upload - post_max_size: " . ini_get('post_max_size'));
error_log("Configuration upload - max_execution_time: " . ini_get('max_execution_time'));
error_log("Configuration upload - memory_limit: " . ini_get('memory_limit'));
?>
