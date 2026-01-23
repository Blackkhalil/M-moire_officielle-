<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/soumission_projet.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Soumettre un projet - SEIC LBS</title>
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
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="login-card">
        <div class="text-center mb-3">
                        <div class="action-card-icon" style="margin:0 auto 1rem; width:60px; height:60px;"><i class="fa-solid fa-lightbulb"></i></div>
                        <h2 class="login-title">Soumettre un projet</h2>
                        <p class="login-subtitle">Proposez un nouveau projet pour la SEIC</p>
        </div>
        <?php if ($success): ?>
                        <div class="alert alert-success login-alert">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            <strong>Succès !</strong> <?= htmlspecialchars($success) ?>
                        </div>
        <?php endif; ?>
        <?php if ($error): ?>
                        <div class="alert alert-danger login-alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            <strong>Erreur !</strong> <?= htmlspecialchars($error) ?>
                        </div>
        <?php endif; ?>
                    <form method="post" class="login-form" autocomplete="on">
                        <div class="form-group">
                            <label for="nom" class="form-label"><i class="fa-solid fa-lightbulb me-1"></i> Nom du projet</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom du projet" required>
                        </div>
                        <div class="form-group">
                            <label for="description" class="form-label"><i class="fa-solid fa-align-left me-1"></i> Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="categorie" class="form-label"><i class="fa-solid fa-tags me-1"></i> Catégorie</label>
                            <select class="form-control" id="categorie" name="categorie" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="Projet énergie et environnement">Projet énergie et environnement</option>
                                <option value="Projet technologie et numérique">Projet technologie et numérique</option>
                                <option value="Projet santé et biotechnologie">Projet santé et biotechnologie</option>
                                <option value="Projet industrie et mobilité">Projet industrie et mobilité</option>
                                <option value="Projet commerce et services">Projet commerce et services</option>
                                <option value="Projet agroalimentaire et innovation agricole">Projet agroalimentaire et innovation agricole</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nom_groupe" class="form-label"><i class="fa-solid fa-users me-1"></i> Nom du groupe</label>
                            <input type="text" class="form-control" id="nom_groupe" name="nom_groupe" placeholder="Nom du groupe" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre_membres" class="form-label"><i class="fa-solid fa-user-group me-1"></i> Nombre de membres</label>
                            <input type="number" class="form-control" id="nombre_membres" name="nombre_membres" placeholder="Nombre de membres" min="1" max="10" required>
                        </div>
                        <div class="form-group">
                            <label for="membres" class="form-label"><i class="fa-solid fa-list me-1"></i> Liste des membres</label>
                            <textarea class="form-control" id="membres" name="membres" rows="3" placeholder="Listez tous les membres du groupe (un par ligne)"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="id_edition" class="form-label"><i class="fa-solid fa-calendar-days me-1"></i> Édition</label>
                            <select class="form-control" id="id_edition" name="id_edition" required>
                                <option value="">Sélectionner une édition</option>
                                <?php foreach ($editions as $edition): ?>
                                    <option value="<?= $edition['id'] ?>"><?= htmlspecialchars($edition['annee']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login-submit"><i class="fa-solid fa-paper-plane me-1"></i>Soumettre</button>
                        <a href="dashboard.php" class="btn btn-outline-primary btn-export-submit mt-2"><i class="fa-solid fa-arrow-left me-1"></i>Retour</a>
                    </form>
                </div>
            </div>
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



