<?php
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || !is_organisateur()) {
    redirect_to_login();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/commentaires.php';

$edition_id = $_GET['id'] ?? null;
$edition = null;
$projets = [];
$presentations_map = [];
$winners = [];

if ($edition_id) {
    // Récupérer les informations de l'édition
    $stmt = $pdo->prepare('SELECT * FROM editions WHERE id = ?');
    $stmt->execute([$edition_id]);
    $edition = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($edition) {
        // Récupérer tous les projets de cette édition
        $stmt = $pdo->prepare('SELECT * FROM projets WHERE id_edition = ? ORDER BY nom');
        $stmt->execute([$edition_id]);
        $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer la dernière présentation (validée de préférence) par projet
        if (!empty($projets)) {
            $projectIds = array_column($projets, 'id');
            $inPlaceholders = implode(',', array_fill(0, count($projectIds), '?'));

            // Dernière validée sinon dernière tout court
            $sql = "
                SELECT p1.* FROM presentations p1
                JOIN (
                    SELECT id_projet, MAX(date_soumission) AS max_date
                    FROM presentations
                    WHERE id_projet IN ($inPlaceholders) AND etat_validation = 'valide'
                    GROUP BY id_projet
                ) sel ON sel.id_projet = p1.id_projet AND sel.max_date = p1.date_soumission
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($projectIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $presentations_map[(int)$row['id_projet']] = $row;
            }

            // Compléter pour ceux sans validée: prendre la dernière tout court
            $missing = array_diff($projectIds, array_keys($presentations_map));
            if (!empty($missing)) {
                $in2 = implode(',', array_fill(0, count($missing), '?'));
                $sql2 = "
                    SELECT p1.* FROM presentations p1
                    JOIN (
                        SELECT id_projet, MAX(date_soumission) AS max_date
                        FROM presentations
                        WHERE id_projet IN ($in2)
                        GROUP BY id_projet
                    ) sel ON sel.id_projet = p1.id_projet AND sel.max_date = p1.date_soumission
                ";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute(array_values($missing));
                foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $presentations_map[(int)$row['id_projet']] = $row;
                }
            }
        }

        // Créer la table des vainqueurs si besoin
        $pdo->exec('CREATE TABLE IF NOT EXISTS winners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_edition INT NOT NULL,
            id_projet INT NOT NULL,
            position TINYINT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_winner (id_edition, position)
        )');

        // Sauvegarde des vainqueurs
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_winners'])) {
            $winner1 = $_POST['winner1'] ?? '';
            $winner2 = $_POST['winner2'] ?? '';
            $winner3 = $_POST['winner3'] ?? '';

            $pdo->prepare('DELETE FROM winners WHERE id_edition = ?')->execute([$edition_id]);

            $insert = $pdo->prepare('INSERT INTO winners (id_edition, id_projet, position) VALUES (?, ?, ?)');
            if (ctype_digit((string)$winner1) && (int)$winner1 > 0) { $insert->execute([$edition_id, (int)$winner1, 1]); }
            if (ctype_digit((string)$winner2) && (int)$winner2 > 0 && $winner2 !== $winner1) { $insert->execute([$edition_id, (int)$winner2, 2]); }
            if (ctype_digit((string)$winner3) && (int)$winner3 > 0 && $winner3 !== $winner1 && $winner3 !== $winner2) { $insert->execute([$edition_id, (int)$winner3, 3]); }
        }

        // Charger les vainqueurs existants
        $stmt = $pdo->prepare('SELECT w.*, p.nom AS nom_projet FROM winners w JOIN projets p ON p.id = w.id_projet WHERE w.id_edition = ? ORDER BY position ASC');
        $stmt->execute([$edition_id]);
        $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les commentaires pour chaque projet
        foreach ($projets as &$projet) {
            $projet['commentaires'] = getCommentairesProjet($pdo, $projet['id']);
            $projet['nb_commentaires'] = countCommentairesProjet($pdo, $projet['id']);
        }
        unset($projet);
    }
}

// Récupérer toutes les éditions pour le menu
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);
?>