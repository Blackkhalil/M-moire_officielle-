<?php
require_once __DIR__ . '/bootstrap.php';
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
$salles = $pdo->query('SELECT * FROM salles ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$groupes = $pdo->query('SELECT g.*, s.nom AS salle_nom FROM groupes g JOIN salles s ON g.id_salle = s.id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Groupes & Salles - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        #plan-img { max-width: 100%; border: 2px solid #ff4000; border-radius: 18px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
        .salle-list { max-height: 400px; overflow-y: auto; }
        .salle-item.active { background: #ff4000; color: #fff; }
        .popover { max-width: 260px; }
    </style>
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
            <h2 class="dashboard-title"><i class="fa-solid fa-users-rectangle me-2"></i>Groupes & Salles</h2>
            <p class="dashboard-subtitle">Liste des salles et des groupes associés</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="action-card">
                    <div class="action-card-header">
                        <div class="action-card-icon"><i class="fa-solid fa-door-open"></i></div>
                        <div>
                            <div class="action-card-title">Salles</div>
                            <p class="action-card-description">Toutes les salles disponibles</p>
                        </div>
                    </div>
                    <ul class="list-group">
                        <?php foreach ($salles as $salle): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-door-open me-1 text-primary"></i> <?= htmlspecialchars($salle['nom']) ?></span>
                                <span class="badge bg-primary"><?= htmlspecialchars($salle['localisation']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="action-card">
                    <div class="action-card-header">
                        <div class="action-card-icon"><i class="fa-solid fa-users"></i></div>
                        <div>
                            <div class="action-card-title">Groupes</div>
                            <p class="action-card-description">Groupes et salles d'affectation</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead>
                                <tr><th>Groupe</th><th>Édition</th><th>Salle</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($groupes as $groupe): ?>
                                <tr>
                                    <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($groupe['nom']) ?></span></td>
                                    <td><?= htmlspecialchars($groupe['annee']) ?></td>
                                    <td><?= htmlspecialchars($groupe['salle_nom']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="groupes_salles.php" class="btn btn-primary me-2"><i class="fa-solid fa-pen-to-square me-1"></i>Gérer Groupes & Salles</a>
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








