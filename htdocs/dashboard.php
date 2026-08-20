<?php
require_once __DIR__ . '/bootstrap.php';
if (!is_logged_in()) {
    redirect_to_login();
}
$user = current_user();
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/logo-lbs.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord - SEIC LBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="logo-container">
                <img src="images/logo-lbs.png" alt="Logo LBS" class="logo-img img-fluid">
                <div class="logo-text">
                    <span class="logo-title">SEIC</span>
                    <span class="logo-subtitle">Lomé Business School</span>
                </div>
            </div>
        </a>
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="toggler-icon"></span>
            <span class="toggler-icon"></span>
            <span class="toggler-icon"></span>
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
            <p class="dashboard-subtitle">Rôle : <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
    <div class="dashboard-actions">
        <?php if ($role === 'organisateur'): ?>
            <a class="action-card" href="editions.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div>
                        <div class="action-card-title">Gérer les Editions</div>
                        <p class="action-card-description">Ajoutez, modifiez ou supprimez les Editions SEIC.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="projets.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-diagram-project"></i></div>
                    <div>
                        <div class="action-card-title">Gérer les projets</div>
                        <p class="action-card-description">Consultez et administrez les projets Etudiants.</p>
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
            <a class="action-card" href="projets_edition.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-list-check"></i></div>
                    <div>
                        <div class="action-card-title">Projets par Edition</div>
                        <p class="action-card-description">Consultez tous les projets d'une Edition SEIC.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="gestion_powerpoint.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-file-powerpoint"></i></div>
                    <div>
                        <div class="action-card-title">Gestion des présentations</div>
                        <p class="action-card-description">Validez et téléchargez les présentations PowerPoint.</p>
                    </div>
                </div>
            </a>
        <?php elseif ($role === 'etudiant'): ?>
            <!-- Section de suivi des soumissions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="action-card">
                        <div class="dashboard-tracking-header">
                            <div class="dashboard-tracking-content">
                                <h4 class="mb-1"><i class="fa-solid fa-chart-line me-2"></i>Suivi de mes soumissions</h4>
                                <p class="mb-0 text-muted">Consultez l'Etat de validation de vos rapports et présentations</p>
                            </div>
                            <a href="suivi_soumissions.php" class="btn btn-outline-primary dashboard-tracking-btn">
                                <i class="fa-solid fa-eye me-1"></i>Voir le détail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
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
            <a class="action-card" href="soumettre_powerpoint.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-file-powerpoint"></i></div>
                    <div>
                        <div class="action-card-title">Soumettre une présentation</div>
                        <p class="action-card-description">Déposez votre présentation PowerPoint.</p>
                    </div>
                </div>
            </a>
            <a class="action-card" href="etudiant_projets.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-eye"></i></div>
                    <div>
                        <div class="action-card-title">Voir les projets</div>
                        <p class="action-card-description">Consultez tous les projets et les commentaires des visiteurs.</p>
                    </div>
                </div>
            </a>
        <?php elseif ($role === 'visiteur'): ?>
            <a class="action-card" href="visitor_projects.php">
                <div class="action-card-header">
                    <div class="action-card-icon"><i class="fa-solid fa-eye"></i></div>
                    <div>
                        <div class="action-card-title">Voir les projets</div>
                        <p class="action-card-description">Consultez tous les projets, groupes et salles.</p>
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
                <strong>&copy; 2024 Lomé Business School</strong> — Plateforme SEIC
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









