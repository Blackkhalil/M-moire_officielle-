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
    
    if ($presentation_id) {
        switch ($action) {
            case 'valider':
                $stmt = $pdo->prepare('UPDATE presentations SET etat_validation = ?, commentaire = ? WHERE id = ?');
                $stmt->execute(['valide', $_POST['commentaire'] ?? '', $presentation_id]);
                $success = 'Présentation validée avec succès !';
                break;
                
            case 'refuser':
                $stmt = $pdo->prepare('UPDATE presentations SET etat_validation = ?, commentaire = ? WHERE id = ?');
                $stmt->execute(['refuse', $_POST['commentaire'] ?? '', $presentation_id]);
                $success = 'Présentation refusée.';
                break;
        }
    }
}

// Récupérer toutes les présentations avec les informations des projets
$stmt = $pdo->prepare('
    SELECT 
        p.*,
        pr.nom as nom_projet,
        pr.nom_groupe,
        pr.categorie,
        e.annee,
        u.nom as nom_etudiant
    FROM presentations p
    JOIN projets pr ON p.id_projet = pr.id
    JOIN editions e ON pr.id_edition = e.id
    LEFT JOIN utilisateurs u ON pr.id_edition = e.id
    ORDER BY p.date_soumission DESC
');
$stmt->execute();
$presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => count($presentations),
    'en_attente' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'en_attente')),
    'validees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'valide')),
    'refusees' => count(array_filter($presentations, fn($p) => $p['etat_validation'] === 'refuse'))
];
?>
