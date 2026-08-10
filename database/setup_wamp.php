<?php
/**
 * Installation de la base seic_lbs sur WAMP (CLI uniquement).
 * Usage : php database/setup_wamp.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Exécuter ce script en ligne de commande : php database/setup_wamp.php');
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'seic_lbs';
$baseDir = __DIR__;

function runSqlFile(PDO $pdo, string $path, bool $ignoreExists = false): void
{
    if (!is_file($path)) {
        throw new RuntimeException("Fichier SQL introuvable : $path");
    }

    $sql = file_get_contents($path);
    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
    if ($ignoreExists) {
        $sql = preg_replace('/CREATE INDEX IF NOT EXISTS/i', 'CREATE INDEX', $sql);
    }
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            if ($ignoreExists && in_array((int) $e->errorInfo[1], [1050, 1061, 1062], true)) {
                continue;
            }
            throw $e;
        }
    }
}

try {
    $pdo = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");

    echo "✓ Base `$dbName` prête\n";

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    echo "✓ Anciennes tables supprimées (si présentes)\n";

    // init.sql corrigé : salles avant groupes (évite la dépendance circulaire)
    $pdo->exec("
        CREATE TABLE utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            mot_de_passe VARCHAR(255) NOT NULL,
            role ENUM('etudiant','organisateur','visiteur') NOT NULL
        );

        CREATE TABLE editions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            annee YEAR NOT NULL,
            description TEXT,
            date_debut DATE,
            date_fin DATE
        );

        CREATE TABLE salles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            localisation VARCHAR(255),
            id_groupe INT NULL
        );

        CREATE TABLE groupes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            id_edition INT,
            id_salle INT,
            nombre_membres INT DEFAULT 0,
            FOREIGN KEY (id_edition) REFERENCES editions(id),
            FOREIGN KEY (id_salle) REFERENCES salles(id)
        );

        ALTER TABLE salles
            ADD FOREIGN KEY (id_groupe) REFERENCES groupes(id);

        CREATE TABLE projets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            description TEXT,
            categorie VARCHAR(100),
            membres TEXT,
            id_edition INT,
            FOREIGN KEY (id_edition) REFERENCES editions(id)
        );

        CREATE TABLE rapports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_projet INT,
            date_soumission DATETIME,
            fichier VARCHAR(255),
            etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
            commentaire TEXT,
            FOREIGN KEY (id_projet) REFERENCES projets(id)
        );
    ");

    $pdo->exec("
        INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
        ('Keith', 'keith@lbs.tg', 'test', 'etudiant'),
        ('Khalil', 'sibikhalil29@gmail.com', 'test', 'organisateur'),
        ('Visiteur Test', 'visiteur@lbs.tg', 'test', 'visiteur'),
        ('Marie Dubois', 'marie@lbs.tg', 'test', 'etudiant'),
        ('Sophie Laurent', 'sophie@lbs.tg', 'test', 'etudiant');

        INSERT INTO editions (annee, description, date_debut, date_fin) VALUES
        (2023, 'SEIC 2023 - Innovation et Entrepreneuriat', '2023-06-01', '2023-06-05'),
        (2024, 'SEIC 2024 - Impact Social', '2024-06-01', '2024-06-05');

        INSERT INTO salles (nom, localisation) VALUES
        ('Salle A', 'Bâtiment 1, RDC'),
        ('Salle B', 'Bâtiment 2, 1er étage');

        INSERT INTO groupes (nom, id_edition, id_salle) VALUES
        ('Groupe Alpha', 1, 1),
        ('Groupe Beta', 2, 2);

        INSERT INTO projets (nom, description, categorie, membres, id_edition) VALUES
        ('Projet Innovant', 'Un projet sur l''innovation.', 'Technologie', 'Keith, Jean K.', 1),
        ('Projet Social', 'Projet à impact social.', 'Impact Social', 'Bob Organisateur, Marie L.', 2),
        ('Application Mobile SEIC', 'Développement d''une application mobile pour la SEIC', 'Technologie', 'Keith, Marie Dubois', 2),
        ('Projet Écologique Campus', 'Solution pour réduire l''empreinte carbone', 'Environnement', 'Keith, Sophie Laurent', 2);

        INSERT INTO rapports (id_projet, date_soumission, fichier, etat_validation, commentaire) VALUES
        (1, '2023-06-02 10:00:00', 'rapport1.pdf', 'valide', 'Très bon rapport.'),
        (2, '2024-06-02 11:00:00', 'rapport2.pdf', 'en_attente', NULL);
    ");

    echo "✓ Schéma initial et données de test\n";

    $migrations = [
        'add_commentaires_table.sql',
        'add_presentations_table.sql',
        'add_user_id_to_submissions.sql',
        'add_projets_groupe_columns.sql',
        'add_presentation_video.sql',
        'optimization_tables.sql' => true,
        'fix_passwords.sql',
        'add_admin.sql',
    ];

    foreach ($migrations as $key => $file) {
        if (is_int($key)) {
            $ignoreExists = false;
        } else {
            $ignoreExists = (bool) $file;
            $file = $key;
        }
        runSqlFile($pdo, $baseDir . DIRECTORY_SEPARATOR . $file, $ignoreExists);
        echo "✓ $file\n";
    }

    $testHash = password_hash('test', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("
        UPDATE utilisateurs
        SET mot_de_passe = ?
        WHERE email IN ('keith@lbs.tg', 'sibikhalil29@gmail.com', 'visiteur@lbs.tg', 'marie@lbs.tg', 'sophie@lbs.tg')
    ");
    $stmt->execute([$testHash]);
    echo "✓ Mots de passe de test définis (test)\n";

    $counts = $pdo->query('
        SELECT
            (SELECT COUNT(*) FROM utilisateurs) AS utilisateurs,
            (SELECT COUNT(*) FROM projets) AS projets,
            (SELECT COUNT(*) FROM rapports) AS rapports
    ')->fetch(PDO::FETCH_ASSOC);

    echo "\n=== Installation terminée ===\n";
    echo "Utilisateurs : {$counts['utilisateurs']}\n";
    echo "Projets      : {$counts['projets']}\n";
    echo "Rapports     : {$counts['rapports']}\n";
    echo "\nComptes de test (mot de passe : test) :\n";
    echo "  - keith@lbs.tg (étudiant)\n";
    echo "  - sibikhalil29@gmail.com (organisateur, mot de passe : test)\n";
    echo "  - visiteur@lbs.tg (visiteur, mot de passe : test)\n";
    echo "\nURL : http://localhost/M-moire_officielle-/htdocs/index.php\n";

} catch (Throwable $e) {
    fwrite(STDERR, "Erreur : " . $e->getMessage() . "\n");
    exit(1);
}
