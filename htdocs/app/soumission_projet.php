<?php
require_once __DIR__ . '/auth.php';
if (!is_logged_in() || current_user()['role'] !== 'etudiant') {
    redirect_to_login();
}
require_once __DIR__ . '/../config/config.php';

$success = '';
$error = '';
$user_id = current_user()['id'];
$editions = $pdo->query('SELECT * FROM editions ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $nom_groupe = $_POST['nom_groupe'] ?? '';
    $nombre_membres = $_POST['nombre_membres'] ?? '';
    $membres = $_POST['membres'] ?? '';
    $id_edition = $_POST['id_edition'] ?? null;
    
    // Normaliser le format des membres : convertir les retours à la ligne en virgules
    // et nettoyer les espaces multiples
    if (!empty($membres)) {
        // Remplacer les retours à la ligne par des virgules
        $membres = str_replace(["\r\n", "\n", "\r"], ',', $membres);
        // Séparer par virgule, nettoyer chaque nom, et reconstruire
        $membres_list = array_map('trim', explode(',', $membres));
        $membres_list = array_filter($membres_list); // Enlever les entrées vides
        $membres = implode(', ', $membres_list); // Rejoindre avec virgule + espace
    }

    // Le créateur doit apparaître dans membres pour etudiant_projets.php
    $nom_createur = trim((string) (current_user()['nom'] ?? ''));
    if ($nom_createur !== '') {
        $deja_present = false;
        foreach (array_map('trim', explode(',', $membres)) as $m) {
            if ($m !== '' && strcasecmp($m, $nom_createur) === 0) {
                $deja_present = true;
                break;
            }
        }
        if (!$deja_present) {
            $membres = $membres === '' ? $nom_createur : $membres . ', ' . $nom_createur;
        }
    }
    
    if ($nom && $categorie && $nom_groupe && $nombre_membres && $id_edition) {
        // Vérifier si la colonne id_utilisateur existe
        $columns_check = $pdo->query("SHOW COLUMNS FROM projets LIKE 'id_utilisateur'")->rowCount();
        if ($columns_check > 0) {
            $stmt = $pdo->prepare('INSERT INTO projets (nom, description, categorie, nom_groupe, nombre_membres, membres, id_edition, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nom, $description, $categorie, $nom_groupe, $nombre_membres, $membres, $id_edition, $user_id]);
        } else {
            // Fallback si la colonne n'existe pas encore
            $stmt = $pdo->prepare('INSERT INTO projets (nom, description, categorie, nom_groupe, nombre_membres, membres, id_edition) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nom, $description, $categorie, $nom_groupe, $nombre_membres, $membres, $id_edition]);
        }
        
        if ($stmt->rowCount() > 0) {
            $success = 'Projet soumis avec succès !';

            require_once __DIR__ . '/mailer.php';
            send_submission_notification_to_organizers(
                $pdo,
                'projet',
                (string) (current_user()['nom'] ?? ''),
                $nom,
                mail_url_to('projets.php')
            );
        } else {
            $error = 'Erreur lors de la soumission.';
        }
    } else {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    }
} 