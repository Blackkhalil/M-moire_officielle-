<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$tokenValid = false;

if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token)) {
    $stmt = $pdo->prepare(
        'SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1'
    );
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset) {
        $tokenValid = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $mot_de_passe2 = $_POST['mot_de_passe2'] ?? '';
    $token = trim($_POST['token'] ?? '');

    if (!verify_csrf_token($csrf)) {
        $error = 'Session expirée. Rechargez la page et réessayez.';
    } elseif ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        $error = 'Lien de réinitialisation invalide.';
        $tokenValid = false;
    } else {
        $stmt = $pdo->prepare(
            'SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1'
        );
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $error = 'Ce lien est invalide ou a expiré. Demandez un nouveau lien.';
            $tokenValid = false;
        } elseif (strlen($mot_de_passe) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
            $tokenValid = true;
        } elseif ($mot_de_passe !== $mot_de_passe2) {
            $error = 'Les mots de passe ne correspondent pas.';
            $tokenValid = true;
        } else {
            $email = strtolower($reset['email']);
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $update = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE LOWER(email) = ?');
            $update->execute([$hash, $email]);

            $delete = $pdo->prepare('DELETE FROM password_resets WHERE token = ?');
            $delete->execute([$token]);

            $deleteOthers = $pdo->prepare('DELETE FROM password_resets WHERE LOWER(email) = ?');
            $deleteOthers->execute([$email]);

            redirect_to('login.php?reset=success');
        }
    }
} elseif ($token !== '' && !$tokenValid) {
    $error = 'Ce lien de réinitialisation est invalide ou a expiré.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/logo-lbs.png">
    <title>Nouveau mot de passe - SEIC LBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    </div>
</nav>
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <h2 class="login-title text-center">Nouveau mot de passe</h2>
                <p class="login-subtitle text-center">Choisissez un mot de passe sécurisé pour votre compte</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger login-alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($tokenValid): ?>
                <form method="post" class="login-form" autocomplete="off">
                    <?= csrf_input() ?>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-group">
                        <label for="mot_de_passe" class="form-label">
                            <i class="fas fa-lock"></i>
                            Nouveau mot de passe
                        </label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe"
                               placeholder="Au moins 8 caractères" minlength="8" required>
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe2" class="form-label">
                            <i class="fas fa-lock"></i>
                            Confirmer le mot de passe
                        </label>
                        <input type="password" class="form-control" id="mot_de_passe2" name="mot_de_passe2"
                               placeholder="Répétez le mot de passe" minlength="8" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login-submit">
                        <i class="fas fa-check"></i>
                        <span>Enregistrer le mot de passe</span>
                    </button>
                </form>
                <?php elseif ($error): ?>
                <div class="login-footer text-center">
                    <p class="login-register-text">
                        <a href="forgot-password.php" class="login-register-link">Demander un nouveau lien</a>
                    </p>
                </div>
                <?php endif; ?>

                <div class="login-footer text-center">
                    <p class="login-register-text">
                        <a href="login.php" class="login-register-link">
                            <i class="fas fa-arrow-left"></i> Retour à la connexion
                        </a>
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
