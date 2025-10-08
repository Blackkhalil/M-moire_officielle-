<?php
require_once __DIR__ . '/../app/auth.php';

// Vérifier la connexion
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user = current_user();
$role = $user['role'];

// Vérifier les permissions
if (!in_array($role, ['administrateur', 'organisateur'])) {
    die('Accès non autorisé');
}

require_once __DIR__ . '/../config/config.php';

// Fonction pour exporter les rapports en PDF
function exportRapportsPDF($pdo) {
    // Récupérer tous les rapports avec les informations des projets
    $stmt = $pdo->prepare("
        SELECT r.*, p.nom as projet_nom, p.description, p.categorie, p.membres, e.annee
        FROM rapports r
        JOIN projets p ON r.id_projet = p.id
        JOIN editions e ON p.id_edition = e.id
        ORDER BY r.date_soumission DESC
    ");
    $stmt->execute();
    $rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Créer le contenu HTML pour le PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Rapports SEIC - Export</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #1e40af; margin-bottom: 10px; }
            .header p { color: #64748b; }
            .rapport { margin-bottom: 25px; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; }
            .rapport h3 { color: #1e40af; margin-bottom: 10px; }
            .info { margin-bottom: 8px; }
            .label { font-weight: bold; color: #334155; }
            .status { padding: 4px 8px; border-radius: 4px; font-size: 0.9em; }
            .status.valide { background: #dcfce7; color: #166534; }
            .status.en_attente { background: #fef3c7; color: #92400e; }
            .status.refuse { background: #fee2e2; color: #991b1b; }
            .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Rapports SEIC - Lomé Business School</h1>
            <p>Export généré le ' . date('d/m/Y à H:i') . '</p>
        </div>
    ';
    
    foreach ($rapports as $rapport) {
        $statusClass = str_replace('_', '', $rapport['etat_validation']);
        $html .= '
        <div class="rapport">
            <h3>' . htmlspecialchars($rapport['projet_nom']) . '</h3>
            <div class="info">
                <span class="label">Projet :</span> ' . htmlspecialchars($rapport['projet_nom']) . '
            </div>
            <div class="info">
                <span class="label">Description :</span> ' . htmlspecialchars($rapport['description']) . '
            </div>
            <div class="info">
                <span class="label">Catégorie :</span> ' . htmlspecialchars($rapport['categorie']) . '
            </div>
            <div class="info">
                <span class="label">Membres :</span> ' . htmlspecialchars($rapport['membres']) . '
            </div>
            <div class="info">
                <span class="label">Édition :</span> ' . $rapport['annee'] . '
            </div>
            <div class="info">
                <span class="label">Date de soumission :</span> ' . date('d/m/Y à H:i', strtotime($rapport['date_soumission'])) . '
            </div>
            <div class="info">
                <span class="label">Fichier :</span> ' . htmlspecialchars($rapport['fichier']) . '
            </div>
            <div class="info">
                <span class="label">État :</span> 
                <span class="status ' . $statusClass . '">' . ucfirst(str_replace('_', ' ', $rapport['etat_validation'])) . '</span>
            </div>';
        
        if ($rapport['commentaire']) {
            $html .= '
            <div class="info">
                <span class="label">Commentaire :</span> ' . htmlspecialchars($rapport['commentaire']) . '
            </div>';
        }
        
        $html .= '</div>';
    }
    
    $html .= '
        <div class="footer">
            <p>Plateforme SEIC - Lomé Business School</p>
            <p>Export généré automatiquement</p>
        </div>
    </body>
    </html>';
    
    return $html;
}

// Fonction pour exporter les projets en PDF
function exportProjetsPDF($pdo) {
    $stmt = $pdo->prepare("
        SELECT p.*, e.annee, COUNT(r.id) as nb_rapports
        FROM projets p
        JOIN editions e ON p.id_edition = e.id
        LEFT JOIN rapports r ON p.id = r.id_projet
        GROUP BY p.id
        ORDER BY p.nom
    ");
    $stmt->execute();
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Projets SEIC - Export</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #1e40af; margin-bottom: 10px; }
            .header p { color: #64748b; }
            .projet { margin-bottom: 25px; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; }
            .projet h3 { color: #1e40af; margin-bottom: 10px; }
            .info { margin-bottom: 8px; }
            .label { font-weight: bold; color: #334155; }
            .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Projets SEIC - Lomé Business School</h1>
            <p>Export généré le ' . date('d/m/Y à H:i') . '</p>
        </div>
    ';
    
    foreach ($projets as $projet) {
        $html .= '
        <div class="projet">
            <h3>' . htmlspecialchars($projet['nom']) . '</h3>
            <div class="info">
                <span class="label">Description :</span> ' . htmlspecialchars($projet['description']) . '
            </div>
            <div class="info">
                <span class="label">Catégorie :</span> ' . htmlspecialchars($projet['categorie']) . '
            </div>
            <div class="info">
                <span class="label">Membres :</span> ' . htmlspecialchars($projet['membres']) . '
            </div>
            <div class="info">
                <span class="label">Édition :</span> ' . $projet['annee'] . '
            </div>
            <div class="info">
                <span class="label">Nombre de rapports :</span> ' . $projet['nb_rapports'] . '
            </div>
        </div>';
    }
    
    $html .= '
        <div class="footer">
            <p>Plateforme SEIC - Lomé Business School</p>
            <p>Export généré automatiquement</p>
        </div>
    </body>
    </html>';
    
    return $html;
}

// Traitement de la requête
$type = $_GET['type'] ?? 'rapports';
$format = $_GET['format'] ?? 'html';

if ($type === 'rapports') {
    $content = exportRapportsPDF($pdo);
    $filename = 'rapports_seic_' . date('Y-m-d_H-i-s') . '.html';
} elseif ($type === 'projets') {
    $content = exportProjetsPDF($pdo);
    $filename = 'projets_seic_' . date('Y-m-d_H-i-s') . '.html';
} else {
    die('Type d\'export non valide');
}

// Envoyer le fichier
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));

echo $content;
?>
