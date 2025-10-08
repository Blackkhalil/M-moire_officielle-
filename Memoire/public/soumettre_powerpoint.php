<?php
require_once __DIR__ . '/../app/soumission_powerpoint.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Soumettre une présentation - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern-style.css">
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
            <div class="col-lg-8 col-md-10">
                <div class="login-card">
                    <div class="text-center mb-3">
                        <div class="action-card-icon" style="margin:0 auto 1rem; width:60px; height:60px;"><i class="fa-solid fa-file-powerpoint"></i></div>
                        <h2 class="login-title">Soumettre une présentation</h2>
                        <p class="login-subtitle">Déposez votre présentation PowerPoint</p>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success login-alert"><i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger login-alert"><i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" class="login-form" enctype="multipart/form-data" autocomplete="on">
                        <div class="form-group">
                            <label for="id_projet" class="form-label"><i class="fa-solid fa-lightbulb me-1"></i> Projet associé</label>
                            <select class="form-control" id="id_projet" name="id_projet" required>
                                <option value="">Sélectionner un projet</option>
                                <?php foreach ($projets as $projet): ?>
                                    <option value="<?= $projet['id'] ?>">
                                        <?= htmlspecialchars($projet['nom']) ?> - SEIC <?= htmlspecialchars($projet['annee']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="presentation" class="form-label"><i class="fa-solid fa-file-powerpoint me-1"></i> Fichier PowerPoint</label>
                            <input type="file" class="form-control" id="presentation" name="presentation" 
                                   accept=".ppt,.pptx" required>
                            <small class="form-text text-muted">
                                Formats acceptés : .ppt et .pptx (Taille max : 50MB)
                            </small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Instructions :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Assurez-vous que votre présentation est complète et prête</li>
                                <li>Vérifiez que tous les éléments multimédias fonctionnent</li>
                                <li>La présentation sera validée par les organisateurs</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-login-submit">
                            <i class="fa-solid fa-upload me-1"></i>Soumettre la présentation
                        </button>
                        
                        <a href="dashboard.php" class="btn btn-outline-primary btn-export-submit mt-2">
                            <i class="fa-solid fa-arrow-left me-1"></i>Retour
                        </a>
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
