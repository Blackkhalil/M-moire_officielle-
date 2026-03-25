<?php
require_once __DIR__ . '/../app/editions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des éditions - SEIC LBS</title>
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
            <h2 class="dashboard-title"><i class="fa-solid fa-calendar-days me-2"></i>Gestion des éditions SEIC</h2>
            <p class="dashboard-subtitle">Créez, modifiez et supprimez les éditions</p>
        </div>
    <div class="action-card mb-4">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-calendar-days fa-2x text-primary me-2"></i>
            <h3 class="fw-bold mb-0">Gestion des éditions SEIC</h3>
        </div>
        <form method="post" class="row g-3 mb-4 align-items-end">
            <?= csrf_input() ?>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="number" name="annee" class="form-control" id="annee" placeholder="Année" required>
                    <label for="annee">Année</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="description" class="form-control" id="description" placeholder="Description">
                    <label for="description">Description</label>
                </div>
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label fw-bold">Début</label>
                <input type="date" name="date_debut" class="form-control" id="date_debut" placeholder="Début">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label fw-bold">Fin</label>
                <input type="date" name="date_fin" class="form-control" id="date_fin" placeholder="Fin">
            </div>
            <div class="col-md-2">
                <button type="submit" name="ajouter" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Ajouter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>Description</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($editions as $edition): ?>
                    <tr>
                        <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($edition['annee']) ?></span></td>
                        <td><?= htmlspecialchars($edition['description']) ?></td>
                        <td><?= htmlspecialchars($edition['date_debut']) ?></td>
                        <td><?= htmlspecialchars($edition['date_fin']) ?></td>
                        <td>
                            <a href="projets_edition.php?id=<?= $edition['id'] ?>" class="btn btn-info btn-sm me-1" title="Voir les projets"><i class="fa-solid fa-eye"></i></a>
                            <a href="?supprimer=<?= $edition['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette édition ?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html> 








