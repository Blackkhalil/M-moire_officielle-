<?php
/**
 * Installation de la base de données SEIC LBS (en ligne de commande uniquement).
 *
 * Usage :
 *   php database/setup_wamp.php                 crée la base et ses 12 tables
 *   php database/setup_wamp.php --demo          ajoute les données de démonstration
 *   php database/setup_wamp.php --reinitialiser efface les tables existantes avant de recréer
 *
 * Options de connexion (valeurs par défaut de WAMP entre parenthèses) :
 *   --hote=localhost  --port=3306  --utilisateur=root  --motdepasse=  --base=seic_lbs
 *
 * Par sécurité, le script s'arrête si la base contient déjà des tables,
 * sauf si l'option --reinitialiser est donnée explicitement.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Exécuter ce script en ligne de commande : php database/setup_wamp.php');
}

$options = [
    'hote' => 'localhost',
    'port' => '3306',
    'utilisateur' => 'root',
    'motdepasse' => '',
    'base' => 'seic_lbs',
];
$avecDemo = false;
$reinitialiser = false;

foreach (array_slice($argv, 1) as $argument) {
    if ($argument === '--demo') {
        $avecDemo = true;
    } elseif ($argument === '--reinitialiser') {
        $reinitialiser = true;
    } elseif (preg_match('/^--(hote|port|utilisateur|motdepasse|base)=(.*)$/', $argument, $m)) {
        $options[$m[1]] = $m[2];
    } else {
        fwrite(STDERR, "Option inconnue : $argument\n");
        exit(2);
    }
}

if (!preg_match('/^[A-Za-z0-9_]+$/', $options['base'])) {
    fwrite(STDERR, "Nom de base invalide : lettres, chiffres et tiret bas uniquement.\n");
    exit(2);
}

/**
 * Exécute un fichier SQL, instruction par instruction.
 */
function executerFichierSql(PDO $pdo, string $chemin): int
{
    if (!is_file($chemin)) {
        throw new RuntimeException("Fichier SQL introuvable : $chemin");
    }

    $sql = file_get_contents($chemin);
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    $instructions = array_filter(array_map('trim', explode(";\n", str_replace("\r\n", "\n", $sql))));

    $nombre = 0;
    foreach ($instructions as $instruction) {
        $instruction = rtrim($instruction, ';');
        if ($instruction === '') {
            continue;
        }
        $pdo->exec($instruction);
        $nombre++;
    }

    return $nombre;
}

try {
    $pdo = new PDO(
        'mysql:host=' . $options['hote'] . ';port=' . (int) $options['port'] . ';charset=utf8mb4',
        $options['utilisateur'],
        $options['motdepasse'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $base = $options['base'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$base` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$base`");
    echo "Base `$base` prête.\n";

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    if ($tables && !$reinitialiser) {
        echo "\nLa base contient déjà " . count($tables) . " table(s) : rien n'a été modifié.\n";
        echo "Pour tout effacer et recréer, relancez avec l'option --reinitialiser\n";
        echo "(attention : toutes les données de cette base seront perdues).\n";
        exit(1);
    }

    if ($tables) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        echo count($tables) . " ancienne(s) table(s) supprimée(s).\n";
    }

    $n = executerFichierSql($pdo, __DIR__ . DIRECTORY_SEPARATOR . 'schema.sql');
    echo "Schéma créé ($n instructions).\n";

    if ($avecDemo) {
        $n = executerFichierSql($pdo, __DIR__ . DIRECTORY_SEPARATOR . 'demo.sql');
        echo "Données de démonstration ajoutées ($n instructions).\n";
        echo "\nComptes d'essai (mot de passe : seic-demo-2026) :\n";
        echo "  etudiant@example.com      étudiant\n";
        echo "  organisateur@example.com  organisateur\n";
        echo "  visiteur@example.com      visiteur\n";
    }

    $nombreTables = count($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN));
    echo "\nInstallation terminée : $nombreTables tables.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Erreur : ' . $e->getMessage() . "\n");
    fwrite(STDERR, "Vérifiez que MySQL est démarré (icône WampServer verte) et que les options de connexion sont justes.\n");
    exit(1);
}
