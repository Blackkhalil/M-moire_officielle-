<?php
require_once __DIR__ . '/app/groupes_salles.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/logo-lbs.png">
    <title>Groupes & Salles - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.7">
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
            <h2 class="dashboard-title"><i class="fa-solid fa-users-rectangle me-2"></i>Gestion des salles et groupes</h2>
            <p class="dashboard-subtitle">Ajoutez, modifiez et supprimez les salles et les groupes</p>
        </div>
    <div class="action-card mb-4">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-users-rectangle fa-2x text-primary me-2"></i>
            <h3 class="fw-bold mb-0">Gestion des salles</h3>
        </div>
        <form method="post" class="row g-3 mb-4 align-items-end">
            <?= csrf_input() ?>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="nom_salle" class="form-control" id="nom_salle" placeholder="Nom de la salle" required>
                    <label for="nom_salle">Nom de la salle</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="localisation" class="form-control" id="localisation" placeholder="Localisation">
                    <label for="localisation">Localisation</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" name="ajouter_salle" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Ajouter salle</button>
            </div>
        </form>
        <div class="table-responsive mb-5">
            <table class="table align-middle table-hover">
                <thead>
                    <tr><th>Nom</th><th>Localisation</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($salles as $salle): ?>
                    <tr>
                        <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($salle['nom']) ?></span></td>
                        <td><?= htmlspecialchars($salle['localisation']) ?></td>
                        <td><a href="?supprimer_salle=<?= $salle['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette salle ?')"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-users fa-2x text-primary me-2"></i>
            <h3 class="fw-bold mb-0">Gestion des groupes</h3>
        </div>
        <form method="post" class="row g-3 mb-4 align-items-end">
            <?= csrf_input() ?>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="nom_groupe" class="form-control" id="nom_groupe" placeholder="Nom du groupe" required>
                    <label for="nom_groupe">Nom du groupe</label>
                </div>
            </div>
            <div class="col-md-3">
                <label for="id_edition" class="form-label fw-bold">Édition</label>
                <select name="id_edition" class="form-select" id="id_edition" required>
                    <option value="">Édition</option>
                    <?php foreach ($editions as $edition): ?>
                        <option value="<?= $edition['id'] ?>"><?= htmlspecialchars($edition['annee']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_salle" class="form-label fw-bold">Salle</label>
                <select name="id_salle" class="form-select" id="id_salle" required>
                    <option value="">Salle</option>
                    <?php foreach ($salles as $salle): ?>
                        <option value="<?= $salle['id'] ?>"><?= htmlspecialchars($salle['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" name="ajouter_groupe" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Ajouter groupe</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr><th>Nom</th><th>Édition</th><th>Salle</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($groupes as $groupe): ?>
                    <tr>
                        <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($groupe['nom']) ?></span></td>
                        <td><?= htmlspecialchars($groupe['annee']) ?></td>
                        <td><?= htmlspecialchars($groupe['salle_nom']) ?></td>
                        <td><a href="?supprimer_groupe=<?= $groupe['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce groupe ?')"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="dashboard.php" class="btn btn-outline-primary mt-3"><i class="fa-solid fa-arrow-left me-1"></i>Retour au tableau de bord</a>
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
</body>
</html> 








