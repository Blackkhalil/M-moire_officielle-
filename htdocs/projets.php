<?php
require_once __DIR__ . '/../app/projets.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des projets - SEIC LBS</title>
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
            <h2 class="dashboard-title"><i class="fa-solid fa-diagram-project me-2"></i>Gestion des projets étudiants</h2>
            <p class="dashboard-subtitle">Créez, modifiez et supprimez les projets</p>
        </div>
    <div class="action-card mb-4">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-diagram-project fa-2x text-primary me-2"></i>
            <h3 class="fw-bold mb-0">Gestion des projets étudiants</h3>
        </div>
        <form method="post" class="row g-3 mb-4 align-items-end">
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" name="nom" class="form-control" id="nom" placeholder="Nom du projet" required>
                    <label for="nom">Nom du projet</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="description" class="form-control" id="description" placeholder="Description">
                    <label for="description">Description</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" name="categorie" class="form-control" id="categorie" placeholder="Catégorie">
                    <label for="categorie">Catégorie</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="membres" class="form-control" id="membres" placeholder="Membres">
                    <label for="membres">Membres</label>
                </div>
            </div>
            <div class="col-md-2">
                <label for="id_edition" class="form-label fw-bold">Édition</label>
                <select name="id_edition" class="form-select" id="id_edition" required>
                    <option value="">Édition</option>
                    <?php foreach ($editions as $edition): ?>
                        <option value="<?= $edition['id'] ?>"><?= htmlspecialchars($edition['annee']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" name="ajouter" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Ajouter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Membres</th>
                        <th>Édition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($projets as $projet): ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary fs-6"><?= htmlspecialchars($projet['nom']) ?></span>
                            <?php if (($projet['nb_commentaires'] ?? 0) > 0): ?>
                                <br><small class="text-muted">
                                    <i class="fa-solid fa-comments me-1"></i>
                                    <?= $projet['nb_commentaires'] ?> commentaire(s)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($projet['description']) ?></td>
                        <td><?= htmlspecialchars($projet['categorie']) ?></td>
                        <td><?= htmlspecialchars($projet['membres']) ?></td>
                        <td><?= htmlspecialchars($projet['id_edition']) ?></td>
                        <td>
                            <a href="?supprimer=<?= $projet['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce projet ?')"><i class="fa-solid fa-trash"></i></a>
                            <?php if (($projet['nb_commentaires'] ?? 0) > 0): ?>
                                <button class="btn btn-info btn-sm mt-1" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#commentaires-<?= $projet['id'] ?>" 
                                        title="Voir les commentaires">
                                    <i class="fa-solid fa-comments"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (($projet['nb_commentaires'] ?? 0) > 0): ?>
                    <tr>
                        <td colspan="6" class="bg-light p-0">
                            <div class="collapse" id="commentaires-<?= $projet['id'] ?>">
                                <div class="p-3">
                                    <h6 class="mb-2"><i class="fa-solid fa-comments me-1"></i>Commentaires des visiteurs</h6>
                                    <div class="commentaires-list" style="max-height: 300px; overflow-y: auto;">
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
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
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



