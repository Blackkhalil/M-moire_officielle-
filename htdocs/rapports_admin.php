<?php
require_once __DIR__ . '/../app/rapports_admin.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des rapports - SEIC LBS</title>
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
            <h2 class="dashboard-title"><i class="fa-solid fa-file-pdf me-2"></i>Gestion des rapports</h2>
            <p class="dashboard-subtitle">Validez et téléchargez les rapports PDF des étudiants</p>
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
                        <h5 class="card-title text-success"><?= $stats['valides'] ?></h5>
                        <p class="card-text">Validés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-danger"><?= $stats['refuses'] ?></h5>
                        <p class="card-text">Refusés</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des rapports -->
        <div class="action-card">
            <h4 class="mb-3"><i class="fa-solid fa-list me-2"></i>Liste des rapports</h4>
            
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
                                <th>Catégorie</th>
                                <th>Membres</th>
                                <th>Édition</th>
                                <th>Fichier</th>
                                <th>Date</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapports as $rapport): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($rapport['nom_projet']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($rapport['categorie']) ?></span>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($rapport['membres']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">SEIC <?= htmlspecialchars($rapport['annee']) ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fa-solid fa-file-pdf text-danger me-1"></i>
                                            <strong><?= htmlspecialchars($rapport['fichier']) ?></strong>
                                        </div>
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
                                        <div class="btn-group" role="group">
                                            <a href="view_pdf.php?id=<?= $rapport['id'] ?>" class="btn btn-info btn-sm" title="Voir">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="download.php?id=<?= $rapport['id'] ?>" class="btn btn-secondary btn-sm" title="Télécharger">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            
                                            <?php if ($rapport['etat_validation'] === 'en_attente'): ?>
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#validationModal" 
                                                        data-id="<?= $rapport['id'] ?>" data-action="valider" title="Valider">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#validationModal" 
                                                        data-id="<?= $rapport['id'] ?>" data-action="refuser" title="Refuser">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($rapport['commentaire']): ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fa-solid fa-comment me-1"></i>
                                                <?= htmlspecialchars($rapport['commentaire']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-3">
            <a href="export_pdf.php?type=rapports" class="btn btn-primary me-2"><i class="fas fa-file-export me-1"></i>Exporter tous les rapports</a>
            <a href="dashboard.php" class="btn btn-outline-primary"><i class="fa-solid fa-arrow-left me-1"></i>Retour au tableau de bord</a>
        </div>
    </div>
</section>

<!-- Modal de validation -->
<div class="modal fade" id="validationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Valider le rapport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="rapport_id" id="modalRapportId">
                    <input type="hidden" name="action" id="modalAction">
                    
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3" 
                                  placeholder="Ajoutez un commentaire sur ce rapport..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn" id="modalSubmitBtn">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('validationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalSubmitBtn = document.getElementById('modalSubmitBtn');
    const modalRapportId = document.getElementById('modalRapportId');
    const modalAction = document.getElementById('modalAction');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const rapportId = button.getAttribute('data-id');
        const action = button.getAttribute('data-action');
        
        modalRapportId.value = rapportId;
        modalAction.value = action;
        
        if (action === 'valider') {
            modalTitle.textContent = 'Valider le rapport';
            modalSubmitBtn.textContent = 'Valider';
            modalSubmitBtn.className = 'btn btn-success';
        } else {
            modalTitle.textContent = 'Refuser le rapport';
            modalSubmitBtn.textContent = 'Refuser';
            modalSubmitBtn.className = 'btn btn-danger';
        }
    });
});
</script>
</body>
</html>