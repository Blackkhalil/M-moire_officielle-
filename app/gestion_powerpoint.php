<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'organisateur') {
    header('Location: ../public/login.php');
    exit;
}
require_once __DIR__ . '/../config/config.php';

$success = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $presentation_id = $_POST['presentation_id'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';
    
    if ($action === 'valider' && $presentation_id) {
        $stmt = $pdo->prepare('UPDATE presentations SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['valide', $commentaire, $presentation_id])) {
            $success = 'Présentation validée avec succès !';
        } else {
            $error = 'Erreur lors de la validation.';
        }
    } elseif ($action === 'refuser' && $presentation_id) {
        $stmt = $pdo->prepare('UPDATE presentations SET etat_validation = ?, commentaire = ? WHERE id = ?');
        if ($stmt->execute(['refuse', $commentaire, $presentation_id])) {
            $success = 'Présentation refusée.';
        } else {
            $error = 'Erreur lors du refus.';
        }
    }
}

// Récupérer toutes les présentations avec les informations des projets
$stmt = $pdo->query('
    SELECT 
        p.*,
        pr.nom as nom_projet,
        pr.categorie,
        pr.nom_groupe,
        pr.nombre_membres,
        e.annee
    FROM presentations p
    JOIN projets pr ON p.id_projet = pr.id
    JOIN editions e ON pr.id_edition = e.id
    ORDER BY p.date_soumission DESC
');
$presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => count($presentations),
    'en_attente' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'en_attente')),
    'validees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'valide')),
    'refusees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'refuse'))
];
?>