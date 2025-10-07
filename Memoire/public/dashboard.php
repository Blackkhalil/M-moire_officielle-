<?php
require_once __DIR__ . '/../app/auth.php';
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
$user = current_user();
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - SEIC LBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.html">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img">
                <div class="logo-text">
                    <span class="logo-title">SEIC</span>
                    <span class="logo-subtitle">Lomé Business School</span>
                </div>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i>Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>
<section class="dashboard-section">
    <div class="container">
        <div class="dashboard-header">
            <h2 class="dashboard-title">Bienvenue, <?= htmlspecialchars($user['nom']) ?></h2>
            <p class="dashboard-subtitle">Rôle : <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($role) ?></span></p>
        </div>
    <div class="dashboard-actions">
        <?php if ($role === 'organisateur'): ?>
            <a class="action-card" href="editions.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div>
                        <div class="action-card-title">Gérer les éditions</div>
                        <p class="action-card-description">Ajoutez, modifiez ou supprimez les éditions SEIC.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="projets.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-diagram-project"></i></div>
                    <div>
                        <div class="action-card-title">Gérer les projets</div>
                        <p class="action-card-description">Consultez et administrez les projets étudiants.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="rapports_admin.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div>
                        <div class="action-card-title">Valider les rapports</div>
                        <p class="action-card-description">Validez/refusez les rapports et ajoutez un commentaire.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="groupes_salles.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-users-rectangle"></i></div>
                    <div>
                        <div class="action-card-title">Groupes & Salles</div>
                        <p class="action-card-description">Attribuez et gérez les salles et groupes.</p>
                    </div>
                </div>
            </a>
        <?php elseif ($role === 'etudiant'): ?>
            <a class="action-card" href="soumettre_projet.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-lightbulb"></i></div>
                    <div>
                        <div class="action-card-title">Soumettre un projet</div>
                        <p class="action-card-description">Proposez un nouveau projet pour la SEIC.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="soumettre_rapport.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-file-upload"></i></div>
                    <div>
                        <div class="action-card-title">Soumettre un rapport</div>
                        <p class="action-card-description">Déposez votre rapport PDF.</p>
                    </div>
                </div>
            </a>
        <?php endif; ?>
    </div>
    <div class="text-center mt-4 mb-4">
        <a href="logout.php" class="btn btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-1"></i>Déconnexion</a>
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