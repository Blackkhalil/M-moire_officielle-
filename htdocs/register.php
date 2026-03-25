<?php
require_once __DIR__ . '/../app/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $mot_de_passe2 = $_POST['mot_de_passe2'] ?? '';
    $role = $_POST['role'] ?? 'etudiant';
    
    // Validation
    if (empty($nom)) {
        $error = 'Le nom est obligatoire.';
    } elseif (empty($email)) {
        $error = 'L\'email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'L\'email n\'est pas valide.';
    } elseif (empty($mot_de_passe)) {
        $error = 'Le mot de passe est obligatoire.';
    } elseif (strlen($mot_de_passe) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractÃ¨res.';
    } elseif ($mot_de_passe !== $mot_de_passe2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (!in_array($role, ['etudiant', 'visiteur'])) {
        $error = 'RÃ´le invalide.';
    } elseif (register($nom, $email, $mot_de_passe, $role)) {
        header('Location: login.php?inscription=success');
        exit;
    } else {
        $error = 'Erreur lors de l\'inscription (email dÃ©jÃ  utilisÃ© ?).';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.4">
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
                    <span class="logo-subtitle">LomÃ© Business School</span>
                </div>
            </div>
        </a>
    </div>
</nav>
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="login-card">
                    <div class="login-header">
                        <div class="login-logo">
                            <img src="images/logo-lbs.png" alt="Logo LBS" class="login-logo-img img-fluid">
                        </div>
                        <h2 class="login-title">Inscription</h2>
                        <p class="login-subtitle">CrÃ©ez votre compte SEIC</p>
                    </div>
                    <?php if ($error): ?>
                        <div class="alert alert-danger login-alert"><i class="fas fa-exclamation-circle"></i><span><?= htmlspecialchars($error) ?></span></div>
                    <?php endif; ?>
                    <form method="post" class="login-form" autocomplete="on">
                        <?= csrf_input() ?>
                        <div class="form-group">
                            <label for="nom" class="form-label"><i class="fa-solid fa-user"></i> Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Votre nom complet" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label"><i class="fa-solid fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse email" required>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe" class="form-label"><i class="fa-solid fa-lock"></i> Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Minimum 8 caractÃ¨res" required minlength="8">
                            <small class="text-muted">Minimum 8 caractÃ¨res requis</small>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe2" class="form-label"><i class="fa-solid fa-lock"></i> Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe2" name="mot_de_passe2" placeholder="RÃ©pÃ©tez le mot de passe" required>
                        </div>
                        <div class="form-group">
                            <label for="role" class="form-label"><i class="fa-solid fa-user-tag"></i> Type de compte</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="etudiant" selected>Ã‰tudiant</option>
                                <option value="visiteur">Visiteur</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                <strong>Ã‰tudiant :</strong> Peut soumettre des projets, rapports et prÃ©sentations<br>
                                <strong>Visiteur :</strong> Peut consulter les projets et laisser des commentaires
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login-submit">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>S'inscrire</span>
                        </button>
                    </form>
                    <div class="login-footer">
                        <p class="login-register-text">DÃ©jÃ  un compte ? <a href="login.php" class="login-register-link">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                <strong>Â© 2024 LomÃ© Business School</strong> â€” Plateforme SEIC
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









