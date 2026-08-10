<?php
require_once __DIR__ . '/app/projets_edition.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projets de l'édition - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img img-fluid">
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
            <h2 class="dashboard-title"><i class="fa-solid fa-lightbulb me-2"></i>Projets de l'édition SEIC</h2>
            <p class="dashboard-subtitle">Consultez tous les projets soumis pour une édition</p>
        </div>
        
        <?php if ($edition): ?>
        <div class="action-card mb-4">
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-calendar-days fa-2x text-primary me-2"></i>
                <div>
                    <h3 class="fw-bold mb-0">Édition SEIC <?= htmlspecialchars($edition['annee']) ?></h3>
                    <p class="text-muted mb-0"><?= htmlspecialchars($edition['description']) ?></p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Période :</strong> 
                    <?= htmlspecialchars($edition['date_debut']) ?> - <?= htmlspecialchars($edition['date_fin']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Nombre de projets :</strong> <?= count($projets) ?>
                </div>
            </div>
        </div>
        
        <?php if (empty($projets)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            Aucun projet soumis pour cette édition.
        </div>
        <?php else: ?>
        <div class="action-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="fa-solid fa-list me-2"></i>Liste des projets</h4>
                <div class="d-flex gap-2">
                    <label class="form-label mb-0 me-2">Filtrer</label>
                    <input type="text" class="form-control w-100" id="filterInput" placeholder="Nom, catégorie, groupe..." >
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="projectsTable">
                    <thead>
                        <tr>
                            <th>Nom du projet</th>
                            <th>Catégorie</th>
                            <th>Groupe</th>
                            <th>Membres</th>
                            <th>Description</th>
                            <th>Présentation</th>
                            <th>Télécharger</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projets as $projet): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($projet['nom']) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?= htmlspecialchars($projet['categorie']) ?></span>
                            </td>
                            <td>
                                <div>
                                    <strong><?= htmlspecialchars((string) ($projet['nom_groupe'] ?? '')) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars((string) ($projet['nombre_membres'] ?? 0)) ?> membre(s)</small>
                                </div>
                            </td>
                            <td>
                                <small><?= nl2br(htmlspecialchars((string) ($projet['membres'] ?? ''))) ?></small>
                            </td>
                            <td>
                                <?php $projet_description = (string) ($projet['description'] ?? ''); ?>
                                <small><?= htmlspecialchars(substr($projet_description, 0, 100)) ?><?= strlen($projet_description) > 100 ? '...' : '' ?></small>
                            </td>
                            <td>
                                <?php $pres = $presentations_map[$projet['id']] ?? null; ?>
                                <?php if ($pres): ?>
                                    <div>
                                        <i class="fa-solid fa-file-powerpoint text-primary me-1"></i>
                                        <strong><?= htmlspecialchars($pres['nom_fichier']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= number_format($pres['taille_fichier'] / 1024 / 1024, 2) ?> MB</small>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Aucune présentation</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($pres)): ?>
                                    <a href="download_powerpoint.php?id=<?= $pres['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="Aucun fichier">
                                        <i class="fa-solid fa-download"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <!-- Commentaires du projet -->
                        <tr>
                            <td colspan="7" class="bg-light">
                                <div class="p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-comments me-1"></i>
                                            <strong><?= $projet['nb_commentaires'] ?? 0 ?></strong> commentaire(s) des visiteurs
                                        </small>
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#commentaires-projet-<?= $projet['id'] ?>" 
                                                aria-expanded="false">
                                            <i class="fa-solid fa-comment-dots me-1"></i>Voir les commentaires
                                        </button>
                                    </div>
                                    
                                    <div class="collapse" id="commentaires-projet-<?= $projet['id'] ?>">
                                        <div class="commentaires-list commentaires-list--lg mt-2">
                                            <?php if (!empty($projet['commentaires'])): ?>
                                                <?php foreach ($projet['commentaires'] as $commentaire): ?>
                                                    <div class="card mb-2 p-2">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="flex-grow-1">
                                                                <small class="fw-bold text-primary">
                                                                    <i class="fa-solid fa-user me-1"></i>
                                                                    <?= htmlspecialchars($commentaire['nom_utilisateur']) ?>
                                                                </small>
                                                                <small class="text-muted ms-2">
                                                                    <i class="fa-solid fa-clock me-1"></i>
                                                                    <?= date('d/m/Y H:i', strtotime($commentaire['date_creation'])) ?>
                                                                </small>
                                                                <p class="mb-0 mt-1 small"><?= nl2br(htmlspecialchars($commentaire['commentaire'])) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted small mb-0">Aucun commentaire pour le moment.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="action-card">
            <h4 class="mb-3"><i class="fa-solid fa-calendar-days me-2"></i>Sélectionner une édition</h4>
            <div class="row">
                <?php foreach ($editions as $ed): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">SEIC <?= htmlspecialchars($ed['annee']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($ed['description']) ?></p>
                            <a href="?id=<?= $ed['id'] ?>" class="btn btn-primary">
                                <i class="fa-solid fa-eye me-1"></i>Voir les projets
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        
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
                <a href="https://www.facebook.com/share/184CCko87w/?mibextid=wwXIfr" class="text-white text-decoration-none ms-3" target="_blank" rel="noopener" aria-label="Facebook LBS"><i class="fab fa-facebook"></i></a>
                <a href="https://www.linkedin.com/school/lom%C3%A9-business-school/posts/?feedView=all" class="text-white text-decoration-none ms-2" target="_blank" rel="noopener" aria-label="LinkedIn LBS"><i class="fab fa-linkedin"></i></a>
                <a href="https://www.instagram.com/lome_business_school_officiel?igsh=ZGZzMmM0aTBjYzd0" class="text-white text-decoration-none ms-2" target="_blank" rel="noopener" aria-label="Instagram LBS"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
// Filtre en temps réel
document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('filterInput');
  const table = document.getElementById('projectsTable');
  if (!input || !table) return;
  input.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    for (const row of table.tBodies[0].rows) {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    }
  });
});
</script>
</body>
</html>









