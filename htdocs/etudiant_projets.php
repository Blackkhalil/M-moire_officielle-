<?php
require_once __DIR__ . '/../app/etudiant_projets.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projets SEIC - Étudiant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.4">
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
            <h2 class="dashboard-title"><i class="fa-solid fa-lightbulb me-2"></i>Mes projets SEIC</h2>
            <p class="dashboard-subtitle">Consultez vos projets et les commentaires des visiteurs</p>
        </div>
        
        <!-- Sélection d'édition -->
        <div class="action-card mb-4">
            <h4 class="mb-3"><i class="fa-solid fa-calendar-days me-2"></i>Sélectionner une édition</h4>
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="edition" class="form-label fw-bold">Édition SEIC</label>
                    <select class="form-control" id="edition" name="edition" onchange="this.form.submit()">
                        <option value="">Choisir une édition</option>
                        <?php foreach ($editions as $ed): ?>
                            <option value="<?= $ed['id'] ?>" <?= $edition_id == $ed['id'] ? 'selected' : '' ?>>
                                SEIC <?= htmlspecialchars($ed['annee']) ?> - <?= htmlspecialchars($ed['description']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        
        <?php if ($edition_id && !empty($projets_data)): ?>
        <!-- Affichage des projets de l'étudiant -->
        <div class="action-card">
            <h4 class="mb-3"><i class="fa-solid fa-user-check me-2"></i>Mes projets</h4>
            <p class="text-muted mb-3">Vous êtes membre de <?= count($projets_data) ?> projet(s) dans cette édition</p>
            
            <div class="row">
                <?php foreach ($projets_data as $projet): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-start border-primary border-3">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fa-solid fa-lightbulb me-1"></i>
                                    <?= htmlspecialchars($projet['nom']) ?>
                                </h6>
                                <?php if ($projet['nom_salle']): ?>
                                    <p class="card-text mb-2">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-door-open me-1"></i>
                                            <strong>Salle :</strong> <?= htmlspecialchars($projet['nom_salle']) ?>
                                            <?php if ($projet['localisation']): ?>
                                                (<?= htmlspecialchars($projet['localisation']) ?>)
                                            <?php endif; ?>
                                        </small>
                                    </p>
                                <?php endif; ?>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <strong>Catégorie :</strong><br>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($projet['categorie']) ?></span>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <strong>Groupe :</strong><br>
                                        <?= htmlspecialchars($projet['nom_groupe']) ?> 
                                        (<?= htmlspecialchars($projet['nombre_membres']) ?> membre(s))
                                    </small>
                                </p>
                                <?php if ($projet['description']): ?>
                                <p class="card-text">
                                    <small>
                                        <strong>Description :</strong><br>
                                        <?= htmlspecialchars(substr($projet['description'], 0, 100)) ?>
                                        <?= strlen($projet['description']) > 100 ? '...' : '' ?>
                                    </small>
                                </p>
                                <?php endif; ?>
                                <?php if ($projet['membres']): ?>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <strong>Membres :</strong><br>
                                        <?= nl2br(htmlspecialchars($projet['membres'])) ?>
                                    </small>
                                </p>
                                <?php endif; ?>
                                
                                <!-- Section Commentaires -->
                                <div class="mt-3 border-top pt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-comments me-1"></i>
                                            <strong><?= $projet['nb_commentaires'] ?? 0 ?></strong> commentaire(s) des visiteurs
                                        </small>
                                        <?php if (($projet['nb_commentaires'] ?? 0) > 0): ?>
                                            <button class="btn btn-sm btn-outline-primary" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#commentaires-etudiant-<?= $projet['id'] ?>" 
                                                    aria-expanded="false">
                                                <i class="fa-solid fa-comment-dots me-1"></i>Voir
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Zone de commentaires (collapsible) -->
                                    <?php if (($projet['nb_commentaires'] ?? 0) > 0): ?>
                                    <div class="collapse" id="commentaires-etudiant-<?= $projet['id'] ?>">
                                        <div class="mt-2">
                                            <!-- Liste des commentaires -->
                                            <div class="commentaires-list" style="max-height: 200px; overflow-y: auto;">
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
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0 mt-2">Aucun commentaire pour le moment.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php elseif ($edition_id && empty($projets_data)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            Vous n'êtes membre d'aucun projet dans cette édition.
        </div>
        <?php else: ?>
        <div class="alert alert-light">
            <i class="fa-solid fa-hand-pointer me-2"></i>
            Veuillez sélectionner une édition pour voir les projets.
        </div>
        <?php endif; ?>
        
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>









