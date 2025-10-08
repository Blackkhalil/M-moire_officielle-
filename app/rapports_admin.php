<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

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

// Validation ou refus d'un rapport
if (isset($_POST['valider'])) {
    $id = (int)$_POST['id'];
    $commentaire = $_POST['commentaire'] ?? '';
    $stmt = $pdo->prepare('UPDATE rapports SET etat_validation = "valide", commentaire = ? WHERE id = ?');
    $stmt->execute([$commentaire, $id]);
    // Notifier les membres du projet
    $rapport = $pdo->prepare('SELECT r.*, p.membres, p.nom AS projet_nom FROM rapports r JOIN projets p ON r.id_projet = p.id WHERE r.id = ?');
    $rapport->execute([$id]);
    if ($row = $rapport->fetch(PDO::FETCH_ASSOC)) {
        $sujet = "[SEIC LBS] Rapport validé : " . $row['projet_nom'];
        $message = "Votre rapport pour le projet '" . $row['projet_nom'] . "' a été validé.\n\nCommentaire :\n" . $commentaire;
        notifier_membres($row['membres'], $sujet, $message);
    }
}
if (isset($_POST['refuser'])) {
    $id = (int)$_POST['id'];
    $commentaire = $_POST['commentaire'] ?? '';
    $stmt = $pdo->prepare('UPDATE rapports SET etat_validation = "refuse", commentaire = ? WHERE id = ?');
    $stmt->execute([$commentaire, $id]);
    // Notifier les membres du projet
    $rapport = $pdo->prepare('SELECT r.*, p.membres, p.nom AS projet_nom FROM rapports r JOIN projets p ON r.id_projet = p.id WHERE r.id = ?');
    $rapport->execute([$id]);
    if ($row = $rapport->fetch(PDO::FETCH_ASSOC)) {
        $sujet = "[SEIC LBS] Rapport refusé : " . $row['projet_nom'];
        $message = "Votre rapport pour le projet '" . $row['projet_nom'] . "' a été refusé.\n\nCommentaire :\n" . $commentaire;
        notifier_membres($row['membres'], $sujet, $message);
    }
}

// Récupération des rapports avec infos projet/groupe/étudiant
$rapports = $pdo->query('SELECT r.*, p.nom AS projet_nom, p.membres, p.id_edition, e.annee
    FROM rapports r
    JOIN projets p ON r.id_projet = p.id
    JOIN editions e ON p.id_edition = e.id
    ORDER BY r.date_soumission DESC')->fetchAll(PDO::FETCH_ASSOC); 