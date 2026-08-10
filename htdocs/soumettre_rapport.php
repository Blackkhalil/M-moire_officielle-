<?php
require_once __DIR__ . '/app/soumission_rapport.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Soumettre un rapport - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.6">
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
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="login-card">
                    <div class="text-center mb-3">
                        <div class="action-card-icon mx-auto mb-3 d-flex align-items-center justify-content-center"><i class="fa-solid fa-file-pdf"></i></div>
                        <h2 class="login-title">Soumettre un rapport</h2>
                        <p class="login-subtitle">Déposez votre rapport PDF pour votre projet</p>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success login-alert"><i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger login-alert"><i class="fa-solid fa-circle-exclamation me-1"></i><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data" class="login-form" autocomplete="on">
                        <?= csrf_input() ?>
                        <div class="form-group">
                            <label for="id_projet" class="form-label"><i class="fa-solid fa-lightbulb me-1"></i> Projet associé</label>
                            <select class="form-control" id="id_projet" name="id_projet" required>
                                <option value="">Sélectionner un projet</option>
                                <?php $selectedId = $_POST['id_projet'] ?? ''; ?>
                                <?php foreach ($projets as $projet): ?>
                                    <option value="<?= $projet['id'] ?>" <?= ($selectedId !== '' && (string)$selectedId === (string)$projet['id']) ? 'selected' : '' ?>><?= htmlspecialchars($projet['nom']) ?> - <?= htmlspecialchars($projet['categorie']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="rapport" class="form-label"><i class="fa-solid fa-file-pdf me-1"></i> Fichier PDF</label>
                            <input type="file" class="form-control" id="rapport" name="rapport" accept=".pdf" required>
                            <small class="form-text text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Format accepté : .pdf | Taille maximale : 50MB
                            </small>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fa-solid fa-lightbulb me-1"></i> Conseils pour votre rapport :</h6>
                            <ul class="mb-0">
                                <li>Assurez-vous que votre rapport est complet et bien structuré</li>
                                <li>Vérifiez que tous les éléments sont lisibles et bien formatés</li>
                                <li>Le fichier sera vérifié par les organisateurs avant validation</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-login-submit">
                            <i class="fa-solid fa-upload me-1"></i>Soumettre le rapport
                        </button>
                        
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









