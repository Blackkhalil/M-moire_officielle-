<?php
/**
 * Gestion administrative des rapports soumis par les étudiants
 * 
 * Ce fichier permet aux organisateurs de :
 * - Voir tous les rapports soumis par les étudiants
 * - Valider ou refuser les rapports avec des commentaires
 * - Envoyer des notifications automatiques aux étudiants
 * - Consulter les statistiques de soumission
 * 
 * @author Votre nom
 * @version 1.0
 */

// On vérifie que l'utilisateur est connecté et qu'il est organisateur
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

// Vérifier si le système de notifications est disponible
$notifications_available = $pdo->query("SHOW TABLES LIKE 'notifications'")->rowCount() > 0;
if ($notifications_available) {
    require_once __DIR__ . '/notification_manager.php';
}

$success = '';
$error = '';

function notifier_membres($projet_membres, $sujet, $message) {
    global $pdo;
    $membres = array_map('trim', explode(',', $projet_membres));
    foreach ($membres as $nom) {
        $stmt = $pdo->prepare('SELECT email FROM utilisateurs WHERE nom = ?');
        $stmt->execute([$nom]);
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Environnement local: pas de SMTP -> journaliser au lieu d'envoyer
            // Pour activer l'envoi SMTP plus tard: utiliser un provider (Mailtrap, Sendgrid) ou configurer php.ini
            error_log('[SEIC NOTIF] TO=' . $user['email'] . ' SUBJECT=' . $sujet . ' MESSAGE=' . str_replace("\n", ' ', $message));
        }
    }
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $rapport_id = $_POST['rapport_id'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';
    
    if ($action === 'valider' && $rapport_id) {
        $stmt = $pdo->prepare('UPDATE rapports SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['valide', $commentaire, $rapport_id])) {
            $success = 'Rapport validé avec succès !';
            
            // Notifier les membres du projet
            $rapport = $pdo->prepare('SELECT r.*, p.membres, p.nom AS projet_nom, p.id as projet_id FROM rapports r JOIN projets p ON r.id_projet = p.id WHERE r.id = ?');
            $rapport->execute([$rapport_id]);
            if ($row = $rapport->fetch(PDO::FETCH_ASSOC)) {
                $sujet = "[SEIC LBS] Rapport validé : " . $row['projet_nom'];
                $message = "Votre rapport pour le projet '" . $row['projet_nom'] . "' a été validé.\n\nCommentaire :\n" . $commentaire;
                notifier_membres($row['membres'], $sujet, $message);
                
                // Notification dans l'application (si disponible)
                if ($notifications_available) {
                    $notificationManager = getNotificationManager($pdo);
                    $notificationManager->notifyProjectMembers(
                        $row['projet_id'],
                        'rapport_valide',
                        'Rapport validé',
                        'Votre rapport "' . $row['projet_nom'] . '" a été validé par les organisateurs.',
                        ['rapport_id' => $rapport_id, 'commentaire' => $commentaire]
                    );
                }
            }
        } else {
            $error = 'Erreur lors de la validation.';
        }
    } elseif ($action === 'refuser' && $rapport_id) {
        $stmt = $pdo->prepare('UPDATE rapports SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['refuse', $commentaire, $rapport_id])) {
            $success = 'Rapport refusé.';
            
            // Notifier les membres du projet
            $rapport = $pdo->prepare('SELECT r.*, p.membres, p.nom AS projet_nom, p.id as projet_id FROM rapports r JOIN projets p ON r.id_projet = p.id WHERE r.id = ?');
            $rapport->execute([$rapport_id]);
            if ($row = $rapport->fetch(PDO::FETCH_ASSOC)) {
                $sujet = "[SEIC LBS] Rapport refusé : " . $row['projet_nom'];
                $message = "Votre rapport pour le projet '" . $row['projet_nom'] . "' a été refusé.\n\nCommentaire :\n" . $commentaire;
                notifier_membres($row['membres'], $sujet, $message);
                
                // Notification dans l'application (si disponible)
                if ($notifications_available) {
                    $notificationManager = getNotificationManager($pdo);
                    $notificationManager->notifyProjectMembers(
                        $row['projet_id'],
                        'rapport_refuse',
                        'Rapport refusé',
                        'Votre rapport "' . $row['projet_nom'] . '" a été refusé. Veuillez consulter les commentaires.',
                        ['rapport_id' => $rapport_id, 'commentaire' => $commentaire]
                    );
                }
            }
        } else {
            $error = 'Erreur lors du refus.';
        }
    }
}

// Récupérer tous les rapports avec les informations des projets
$stmt = $pdo->query('
    SELECT 
        r.*,
        p.nom as nom_projet,
        p.categorie,
        p.membres,
        e.annee
    FROM rapports r
    JOIN projets p ON r.id_projet = p.id
    JOIN editions e ON p.id_edition = e.id
    ORDER BY r.date_soumission DESC
');
$rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => count($rapports),
    'en_attente' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'en_attente')),
    'valides' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'valide')),
    'refuses' => count(array_filter($rapports, fn($r) => $r['etat_validation'] === 'refuse'))
];