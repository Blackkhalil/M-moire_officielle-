<?php
require_once __DIR__ . '/../app/suivi_soumissions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi des soumissions - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <h2 class="dashboard-title"><i class="fa-solid fa-chart-line me-2"></i>Suivi de mes soumissions</h2>
            <p class="dashboard-subtitle">Consultez l'état de validation de vos rapports et présentations</p>
        </div>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= $stats['rapports_total'] ?></h5>
                        <p class="card-text">Rapports soumis</p>
                        <div class="small text-muted">
                            <span class="badge bg-success me-1"><?= $stats['rapports_valides'] ?> Validés</span>
                            <span class="badge bg-warning me-1"><?= $stats['rapports_en_attente'] ?> En attente</span>
                            <span class="badge bg-danger"><?= $stats['rapports_refuses'] ?> Refusés</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= $stats['presentations_total'] ?></h5>
                        <p class="card-text">Présentations soumises</p>
                        <div class="small text-muted">
                            <span class="badge bg-success me-1"><?= $stats['presentations_valides'] ?> Validées</span>
                            <span class="badge bg-warning me-1"><?= $stats['presentations_en_attente'] ?> En attente</span>
                            <span class="badge bg-danger"><?= $stats['presentations_refusees'] ?> Refusées</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rapports -->
        <div class="action-card mb-4">
            <h4 class="mb-3"><i class="fa-solid fa-file-pdf me-2"></i>Mes rapports</h4>
            
            <?php if (empty($rapports)): ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Aucun rapport soumis pour le moment.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Édition</th>
                                <th>Date de soumission</th>
                                <th>État</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapports as $rapport): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($rapport['projet_nom']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($rapport['categorie']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">SEIC <?= htmlspecialchars($rapport['annee']) ?></span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($rapport['date_soumission'])) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = match($rapport['etat_validation']) {
                                            'valide' => 'bg-success',
                                            'refuse' => 'bg-danger',
                                            default => 'bg-warning'
                                        };
                                        $etat_text = match($rapport['etat_validation']) {
                                            'valide' => 'Validé',
                                            'refuse' => 'Refusé',
                                            default => 'En attente'
                                        };
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $etat_text ?></span>
                                    </td>
                                    <td>
                                        <?php if ($rapport['commentaire']): ?>
                                            <small class="text-muted">
                                                <i class="fa-solid fa-comment me-1"></i>
                                                <?= htmlspecialchars($rapport['commentaire']) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Présentations -->
        <div class="action-card mb-4">
            <h4 class="mb-3"><i class="fa-solid fa-file-powerpoint me-2"></i>Mes présentations</h4>
            
            <?php if (empty($presentations)): ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Aucune présentation soumise pour le moment.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Édition</th>
                                <th>Date de soumission</th>
                                <th>État</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($presentations as $presentation): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($presentation['projet_nom']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($presentation['categorie']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">SEIC <?= htmlspecialchars($presentation['annee']) ?></span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($presentation['date_soumission'])) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = match($presentation['etat_validation']) {
                                            'valide' => 'bg-success',
                                            'refuse' => 'bg-danger',
                                            default => 'bg-warning'
                                        };
                                        $etat_text = match($presentation['etat_validation']) {
                                            'valide' => 'Validée',
                                            'refuse' => 'Refusée',
                                            default => 'En attente'
                                        };
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $etat_text ?></span>
                                    </td>
                                    <td>
                                        <?php if ($presentation['commentaire']): ?>
                                            <small class="text-muted">
                                                <i class="fa-solid fa-comment me-1"></i>
                                                <?= htmlspecialchars($presentation['commentaire']) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
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
