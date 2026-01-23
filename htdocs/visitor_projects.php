<?php
require_once __DIR__ . '/../app/visitor_projects.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projets SEIC - Visiteur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern-style.css?v=2.3">
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
            <h2 class="dashboard-title"><i class="fa-solid fa-eye me-2"></i>Projets SEIC</h2>
            <p class="dashboard-subtitle">Découvrez tous les projets et leur localisation</p>
            
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
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
        <!-- Affichage par salle -->
        <div class="action-card">
            <h4 class="mb-3"><i class="fa-solid fa-map-marker-alt me-2"></i>Répartition par salle</h4>
            
            <?php
            // Grouper les projets par salle
            $projets_par_salle = [];
            foreach ($projets_data as $projet) {
                $salle_key = $projet['nom_salle'] ?: 'Non assigné';
                if (!isset($projets_par_salle[$salle_key])) {
                    $projets_par_salle[$salle_key] = [
                        'localisation' => $projet['localisation'],
                        'projets' => []
                    ];
                }
                $projets_par_salle[$salle_key]['projets'][] = $projet;
            }
            ?>
            
            <?php foreach ($projets_par_salle as $salle_nom => $salle_data): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-door-open me-2"></i>
                        <?= htmlspecialchars($salle_nom) ?>
                        <?php if ($salle_data['localisation']): ?>
                            <small class="ms-2">(<?= htmlspecialchars($salle_data['localisation']) ?>)</small>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($salle_data['projets'] as $projet): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-start border-primary border-3">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fa-solid fa-lightbulb me-1"></i>
                                        <?= htmlspecialchars($projet['nom']) ?>
                                    </h6>
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
                                                <strong><?= $projet['nb_commentaires'] ?? 0 ?></strong> commentaire(s)
                                            </small>
                                            <button class="btn btn-sm btn-outline-primary" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#commentaires-<?= $projet['id'] ?>" 
                                                    aria-expanded="false">
                                                <i class="fa-solid fa-comment-dots me-1"></i>Voir/Commenter
                                            </button>
                                        </div>
                                        
                                        <!-- Zone de commentaires (collapsible) -->
                                        <div class="collapse" id="commentaires-<?= $projet['id'] ?>">
                                            <div class="mt-2">
                                                <!-- Formulaire d'ajout de commentaire -->
                                                <form method="post" class="mb-3">
                                                    <input type="hidden" name="action" value="ajouter">
                                                    <input type="hidden" name="id_projet" value="<?= $projet['id'] ?>">
                                                    <div class="mb-2">
                                                        <textarea class="form-control form-control-sm" 
                                                                  name="commentaire" 
                                                                  rows="2" 
                                                                  placeholder="Ajouter un commentaire..." 
                                                                  required 
                                                                  maxlength="1000"></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-paper-plane me-1"></i>Publier
                                                    </button>
                                                </form>
                                                
                                                <!-- Liste des commentaires -->
                                                <div class="commentaires-list" style="max-height: 200px; overflow-y: auto;">
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
                                                                            <?= date('d/m/Y H:i', strtotime($commentaire['date_creation'])) ?>
                                                                        </small>
                                                                        <p class="mb-0 mt-1 small"><?= nl2br(htmlspecialchars($commentaire['commentaire'])) ?></p>
                                                                    </div>
                                                                    <?php if ($commentaire['id_utilisateur'] == current_user()['id']): ?>
                                                                        <form method="post" class="ms-2" 
                                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                                                            <input type="hidden" name="action" value="supprimer">
                                                                            <input type="hidden" name="commentaire_id" value="<?= $commentaire['id'] ?>">
                                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Supprimer">
                                                                                <i class="fa-solid fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted small mb-0">Aucun commentaire pour le moment.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php elseif ($edition_id && empty($projets_data)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            Aucun projet trouvé pour cette édition.
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



