<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';
$success = '';
$redirect = safe_redirect_target($_POST['redirect'] ?? $_GET['redirect'] ?? null);

if (isset($_GET['inscription']) && $_GET['inscription'] === 'success') {
    $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
}

if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $success = 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = 'Session expirée. Rechargez la page et réessayez.';
    } elseif (login($email, $mot_de_passe)) {
        // $redirect provient de REQUEST_URI et contient déjà le préfixe BASE_URL :
        // on redirige directement dessus (redirect_to()/url_to() le préfixeraient une 2e fois).
        if ($redirect) {
            header('Location: ' . $redirect);
            exit;
        }
        redirect_to('dashboard.php');
    } else {
        $error = 'Email ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/modern-style.css?v=2.6">
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
    </div>
</nav>
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <h2 class="login-title text-center">Connexion</h2>
                <p class="login-subtitle text-center">Accédez à votre espace SEIC</p>

                <?php if ($success): ?>
                    <div class="alert alert-success login-alert">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger login-alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" class="login-form" autocomplete="on">
                    <?= csrf_input() ?>
                    <?php if ($redirect): ?>
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse email" required>
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe" class="form-label">
                            <i class="fas fa-lock"></i>
                            Mot de passe
                        </label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Votre mot de passe" required>
                    </div>

                    <p class="text-end mb-3">
                        <a href="forgot-password.php" class="login-register-link">Mot de passe oublié ?</a>
                    </p>

                    <button type="submit" class="btn btn-primary btn-login-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Se connecter</span>
                    </button>
                </form>

                <div class="login-footer text-center">
                    <p class="login-register-text">
                        Pas encore de compte ?
                        <a href="register.php" class="login-register-link">S'inscrire</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                <strong>&copy; 2024 Lomé Business School</strong> — Plateforme SEIC
            </div>
            <div class="col-md-6 text-md-end text-center">
                <a href="https://lome-bs.com/" class="text-white text-decoration-none me-3" target="_blank" rel="noopener"><i class="fas fa-globe me-1"></i>Site LBS</a>
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
