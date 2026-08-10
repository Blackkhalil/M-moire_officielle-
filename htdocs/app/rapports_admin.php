<?php
/**
 * Gestion administrative des rapports soumis par les étudiants
 * 
 * Ce fichier permet aux organisateurs de :
 * - Voir tous les rapports soumis par les étudiants
 * - Valider ou refuser les rapports avec des commentaires
 * - Envoyer des emails automatiques aux étudiants
 * - Consulter les statistiques de soumission
 * 
 * @author Khalil
 * @version 1.0
 */

// On vérifie que l'utilisateur est connecté et qu'il est organisateur
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || !is_organisateur()) {
    redirect_to_login();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/mailer.php';

$success = '';
$error = '';

function notify_student_of_rapport_validation(PDO $pdo, $rapport_id, string $status, string $commentaire): void
{
    $stmt = $pdo->prepare(
        'SELECT p.nom AS projet_nom, u.nom AS submitter_nom, u.email AS submitter_email
         FROM rapports r
         JOIN projets p ON r.id_projet = p.id
         LEFT JOIN utilisateurs u ON r.id_utilisateur = u.id
         WHERE r.id = ?'
    );
    $stmt->execute([$rapport_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['submitter_email'])) {
        send_validation_notification_to_student(
            $row['submitter_email'],
            (string) $row['submitter_nom'],
            'rapport',
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
    $rapport_id = $_POST['rapport_id'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';

    if ($action === 'valider' && $rapport_id) {
        $stmt = $pdo->prepare('UPDATE rapports SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['valide', $commentaire, $rapport_id])) {
            $success = 'Rapport validé avec succès !';
            notify_student_of_rapport_validation($pdo, $rapport_id, 'valide', $commentaire);
        } else {
            $error = 'Erreur lors de la validation.';
        }
    } elseif ($action === 'refuser' && $rapport_id) {
        $stmt = $pdo->prepare('UPDATE rapports SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['refuse', $commentaire, $rapport_id])) {
            $success = 'Rapport refusé.';
            notify_student_of_rapport_validation($pdo, $rapport_id, 'refuse', $commentaire);
        } else {
            $error = 'Erreur lors du refus.';
        }
    }
}

// Vérifier si la colonne id_utilisateur existe
$has_user_id = $pdo->query("SHOW COLUMNS FROM rapports LIKE 'id_utilisateur'")->rowCount() > 0;

// Récupérer tous les rapports avec les informations des projets et de l'utilisateur
if ($has_user_id) {
    $stmt = $pdo->query('
        SELECT 
            r.*,
            p.nom as nom_projet,
            p.categorie,
            p.membres,
            e.annee,
            u.nom as nom_utilisateur,
            u.email as email_utilisateur
        FROM rapports r
        JOIN projets p ON r.id_projet = p.id
        JOIN editions e ON p.id_edition = e.id
        LEFT JOIN utilisateurs u ON r.id_utilisateur = u.id
        ORDER BY r.date_soumission DESC
    ');
} else {
    $stmt = $pdo->query('
        SELECT 
            r.*,
            p.nom as nom_projet,
            p.categorie,
            p.membres,
            e.annee,
            NULL as nom_utilisateur,
            NULL as email_utilisateur
        FROM rapports r
        JOIN projets p ON r.id_projet = p.id
        JOIN editions e ON p.id_edition = e.id
        ORDER BY r.date_soumission DESC
    ');
}
$rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => count($rapports),
    'en_attente' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'en_attente')),
    'valides' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'valide')),
    'refuses' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'refuse'))
];