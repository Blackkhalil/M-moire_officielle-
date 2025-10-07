<?php
require_once __DIR__ . '/../app/rapports_admin.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des rapports - SEIC LBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img">
                <div class="logo-text">
                    <span class="logo-title">SEIC</span>
                    <span class="logo-subtitle">Lomé Business School</span>
                </div>
            </div>
        </a>
    </div>
</nav>

<section class="dashboard-section">
<div class="container">
    <div class="dashboard-header">
        <h2 class="dashboard-title"><i class="fas fa-file-signature me-2"></i>Validation des rapports étudiants</h2>
        <p class="dashboard-subtitle">Consultez, téléchargez et validez les rapports soumis par les étudiants</p>
    </div>

    <div class="table-responsive">
    <table class="table align-middle table-hover">
        <thead>
            <tr>
                <th>Projet</th>
                <th>Édition</th>
                <th>Membres</th>
                <th>Date de soumission</th>
                <th>Fichier</th>
                <th>État</th>
                <th>Commentaire</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rapports as $rapport): ?>
            <tr>
                <td><?= htmlspecialchars($rapport['projet_nom']) ?></td>
                <td><?= htmlspecialchars($rapport['annee']) ?></td>
                <td><?= htmlspecialchars($rapport['membres']) ?></td>
                <td><?= htmlspecialchars($rapport['date_soumission']) ?></td>
                <td class="d-flex gap-2">
                    <a class="btn btn-outline-primary btn-sm" href="view_pdf.php?id=<?= (int)$rapport['id'] ?>">
                        <i class="fas fa-eye me-1"></i>Voir
                    </a>
                    <a class="btn btn-outline-secondary btn-sm" href="download.php?id=<?= (int)$rapport['id'] ?>">
                        <i class="fas fa-download me-1"></i>Télécharger
                    </a>
                </td>
                <td>
                    <?php if ($rapport['etat_validation'] === 'valide'): ?>
                        <span class="badge bg-success">Validé</span>
                    <?php elseif ($rapport['etat_validation'] === 'refuse'): ?>
                        <span class="badge bg-danger">Refusé</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">En attente</span>
                    <?php endif; ?>
                </td>
                <td><?= nl2br(htmlspecialchars($rapport['commentaire'])) ?></td>
                <td>
                    <?php if ($rapport['etat_validation'] === 'en_attente'): ?>
                    <form method="post" class="d-flex flex-column gap-1">
                        <input type="hidden" name="id" value="<?= $rapport['id'] ?>">
                        <textarea name="commentaire" class="form-control mb-1" placeholder="Commentaire"></textarea>
                        <button type="submit" name="valider" class="btn btn-success btn-sm">Valider</button>
                        <button type="submit" name="refuser" class="btn btn-danger btn-sm">Refuser</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="mt-3">
        <a href="export_pdf.php?type=rapports" class="btn btn-primary me-2"><i class="fas fa-file-export me-1"></i>Exporter tous les rapports</a>
        <a href="dashboard.php" class="btn btn-outline-primary"><i class="fa-solid fa-arrow-left me-1"></i>Retour au tableau de bord</a>
    </div>
</div>
</section>
<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                <strong>© 2024 Lomé Business School</strong> — Plateforme SEIC
            </div>
            <div class="col-md-6 text-md-end text-center">
                <a href="https://lome-bs.com/" class="text-white text-decoration-none me-3" target="_blank"><i class="fas fa-globe me-1"></i>Site LBS</a>
                <a href="mailto:info@lomebs.com" class="text-white text-decoration-none"><i class="fas fa-envelope me-1"></i>Contact</a>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>