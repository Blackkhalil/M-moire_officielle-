<?php
require_once __DIR__ . '/../app/gestion_powerpoint.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des présentations - SEIC LBS</title>
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
            <h2 class="dashboard-title"><i class="fa-solid fa-file-powerpoint me-2"></i>Gestion des présentations</h2>
            <p class="dashboard-subtitle">Validez et gérez les présentations PowerPoint des étudiants</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= $stats['total'] ?></h5>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning"><?= $stats['en_attente'] ?></h5>
                        <p class="card-text">En attente</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success"><?= $stats['validees'] ?></h5>
                        <p class="card-text">Validées</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-danger"><?= $stats['refusees'] ?></h5>
                        <p class="card-text">Refusées</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des présentations -->
        <div class="action-card">
            <h4 class="mb-3"><i class="fa-solid fa-list me-2"></i>Présentations soumises</h4>
            
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
                                <th>Groupe</th>
                                <th>Édition</th>
                                <th>Fichier</th>
                                <th>Date</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($presentations as $presentation): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($presentation['nom_projet']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($presentation['categorie']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($presentation['nom_groupe']) ?></td>
                                    <td>
                                        <span class="badge bg-primary">SEIC <?= htmlspecialchars($presentation['annee']) ?></span>
                                    </td>
                                    <td>
                                        <i class="fa-solid fa-file-powerpoint text-primary me-1"></i>
                                        <?= htmlspecialchars($presentation['nom_fichier']) ?>
                                        <br>
                                        <small class="text-muted"><?= number_format($presentation['taille_fichier'] / 1024, 1) ?> KB</small>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($presentation['date_soumission'])) ?></td>
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
                                        <div class="btn-group" role="group">
                                            <a href="download_powerpoint.php?id=<?= $presentation['id'] ?>" 
                                               class="btn btn-info btn-sm" title="Télécharger">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            
                                            <?php if ($presentation['etat_validation'] === 'en_attente'): ?>
                                                <button type="button" class="btn btn-success btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#validationModal<?= $presentation['id'] ?>"
                                                        title="Valider">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#refusModal<?= $presentation['id'] ?>"
                                                        title="Refuser">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Modal de validation -->
                                <div class="modal fade" id="validationModal<?= $presentation['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Valider la présentation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <p>Valider la présentation : <strong><?= htmlspecialchars($presentation['nom_projet']) ?></strong> ?</p>
                                                    <div class="mb-3">
                                                        <label for="commentaire_valide<?= $presentation['id'] ?>" class="form-label">Commentaire (optionnel)</label>
                                                        <textarea class="form-control" id="commentaire_valide<?= $presentation['id'] ?>" 
                                                                  name="commentaire" rows="3" placeholder="Commentaire de validation..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa-solid fa-check me-1"></i>Valider
                                                    </button>
                                                </div>
                                                <input type="hidden" name="action" value="valider">
                                                <input type="hidden" name="presentation_id" value="<?= $presentation['id'] ?>">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal de refus -->
                                <div class="modal fade" id="refusModal<?= $presentation['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Refuser la présentation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <p>Refuser la présentation : <strong><?= htmlspecialchars($presentation['nom_projet']) ?></strong> ?</p>
                                                    <div class="mb-3">
                                                        <label for="commentaire_refuse<?= $presentation['id'] ?>" class="form-label">Motif du refus</label>
                                                        <textarea class="form-control" id="commentaire_refuse<?= $presentation['id'] ?>" 
                                                                  name="commentaire" rows="3" placeholder="Expliquez pourquoi la présentation est refusée..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa-solid fa-times me-1"></i>Refuser
                                                    </button>
                                                </div>
                                                <input type="hidden" name="action" value="refuser">
                                                <input type="hidden" name="presentation_id" value="<?= $presentation['id'] ?>">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <a href="dashboard.php" class="btn btn-outline-primary mt-3">
            <i class="fa-solid fa-arrow-left me-1"></i>Retour au tableau de bord
        </a>
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
