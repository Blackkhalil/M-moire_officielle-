<?php
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || !is_organisateur()) {
    redirect_to('login.php');
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/mailer.php';

$success = '';
$error = '';

function notify_student_of_powerpoint_validation(PDO $pdo, $presentation_id, string $status, string $commentaire): void
{
    $stmt = $pdo->prepare(
        'SELECT p.nom AS projet_nom, u.nom AS submitter_nom, u.email AS submitter_email
         FROM presentations pr
         JOIN projets p ON pr.id_projet = p.id
         LEFT JOIN utilisateurs u ON pr.id_utilisateur = u.id
         WHERE pr.id = ?'
    );
    $stmt->execute([$presentation_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['submitter_email'])) {
        send_validation_notification_to_student(
            $row['submitter_email'],
            (string) $row['submitter_nom'],
            'powerpoint',
            $row['projet_nom'],
            $status,
            url_to('suivi_soumissions.php'),
            $commentaire !== '' ? $commentaire : null
        );
    }
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';
    $presentation_id = $_POST['presentation_id'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';

    if ($action === 'valider' && $presentation_id) {
        $stmt = $pdo->prepare('UPDATE presentations SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['valide', $commentaire, $presentation_id])) {
            $success = 'Présentation validée avec succès !';
            notify_student_of_powerpoint_validation($pdo, $presentation_id, 'valide', $commentaire);
        } else {
            $error = 'Erreur lors de la validation.';
        }
    } elseif ($action === 'refuser' && $presentation_id) {
        $stmt = $pdo->prepare('UPDATE presentations SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['refuse', $commentaire, $presentation_id])) {
            $success = 'Présentation refusée.';
            notify_student_of_powerpoint_validation($pdo, $presentation_id, 'refuse', $commentaire);
        } else {
            $error = 'Erreur lors du refus.';
        }
    }
}

// Vérifier si la colonne id_utilisateur existe
$has_user_id = $pdo->query("SHOW COLUMNS FROM presentations LIKE 'id_utilisateur'")->rowCount() > 0;

// Récupérer toutes les présentations avec les informations des projets et de l'utilisateur
if ($has_user_id) {
    $stmt = $pdo->query('
        SELECT 
            p.*,
            pr.nom as nom_projet,
            pr.categorie,
            pr.nom_groupe,
            pr.nombre_membres,
            e.annee,
            u.nom as nom_utilisateur,
            u.email as email_utilisateur
        FROM presentations p
        JOIN projets pr ON p.id_projet = pr.id
        JOIN editions e ON pr.id_edition = e.id
        LEFT JOIN utilisateurs u ON p.id_utilisateur = u.id
        ORDER BY p.date_soumission DESC
    ');
} else {
    $stmt = $pdo->query('
        SELECT 
            p.*,
            pr.nom as nom_projet,
            pr.categorie,
            pr.nom_groupe,
            pr.nombre_membres,
            e.annee,
            NULL as nom_utilisateur,
            NULL as email_utilisateur
        FROM presentations p
        JOIN projets pr ON p.id_projet = pr.id
        JOIN editions e ON pr.id_edition = e.id
        ORDER BY p.date_soumission DESC
    ');
}
$presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => count($presentations),
    'en_attente' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'en_attente')),
    'validees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'valide')),
    'refusees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'refuse'))
];
?>