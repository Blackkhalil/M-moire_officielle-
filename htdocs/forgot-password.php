<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/app/mail_config.php';

$mailSettings = mailtrap_settings();
$MAILTRAP_HOST = $mailSettings['host'];
$MAILTRAP_PORT = $mailSettings['port'];
$MAILTRAP_USER = $mailSettings['user'];
$MAILTRAP_PASS = $mailSettings['pass'];
$MAILTRAP_FROM_EMAIL = $mailSettings['from_email'];
$MAILTRAP_FROM_NAME = $mailSettings['from_name'];
$MAILTRAP_ENCRYPTION = $mailSettings['encryption'];
$RESET_PASSWORD_BASE_URL = env_value(
    'RESET_PASSWORD_BASE_URL',
    'http://localhost/M-moire_officielle-/htdocs'
);

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($composerAutoload)) {
    die('PHPMailer non installé. Exécutez « composer install » à la racine du projet.');
}
require_once $composerAutoload;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = 'Session expirée. Rechargez la page et réessayez.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez saisir une adresse e-mail valide.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE LOWER(email) = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($MAILTRAP_USER === '' || $MAILTRAP_PASS === '') {
                error_log('Configuration Mailtrap manquante : MAILTRAP_USER / MAILTRAP_PASS dans .env');
                $error = APP_ENV !== 'production'
                    ? 'Configuration e-mail incomplète. Renseignez MAILTRAP_USER et MAILTRAP_PASS dans le fichier .env.'
                    : 'Une erreur technique est survenue. Veuillez réessayer plus tard.';
            } else {
                $token = bin2hex(random_bytes(32));

                $deleteOld = $pdo->prepare('DELETE FROM password_resets WHERE LOWER(email) = ?');
                $deleteOld->execute([$email]);

                // L'expiration est calculée par MySQL (NOW() + INTERVAL) plutôt qu'en PHP :
                // évite tout décalage entre l'horloge PHP et l'horloge MySQL (timezone système),
                // qui rendait le lien expiré dès sa création.
                $insert = $pdo->prepare(
                    'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, NOW() + INTERVAL 15 MINUTE)'
                );
                $insert->execute([$email, $token]);

                $resetLink = rtrim($RESET_PASSWORD_BASE_URL, '/') . '/reset-password.php?token=' . urlencode($token);

                try {
                    $mail = new PHPMailer(true);
                    configure_phpmailer_smtp($mail, $mailSettings);

                    $mail->setFrom($MAILTRAP_FROM_EMAIL, $MAILTRAP_FROM_NAME);
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Réinitialisation de votre mot de passe — SEIC LBS';
                    $mail->Body = '<p>Bonjour,</p>'
                        . '<p>Vous avez demandé la réinitialisation de votre mot de passe SEIC LBS.</p>'
                        . '<p><a href="' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '">Cliquez ici pour choisir un nouveau mot de passe</a></p>'
                        . '<p>Ce lien expire dans 15 minutes.</p>'
                        . '<p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet e-mail.</p>';
                    $mail->AltBody = "Réinitialisez votre mot de passe : {$resetLink}\nCe lien expire dans 15 minutes.";

                    $mail->send();
                } catch (MailException $e) {
                    $smtpError = $e->getMessage();
                    error_log('Erreur envoi e-mail reset mot de passe : ' . $smtpError);
                    if (APP_ENV !== 'production') {
                        $error = mailtrap_smtp_error_message($smtpError, $mailSettings);
                    }
                }
            }
        }

        if ($error === '') {
            $success = password_reset_success_message();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié - SEIC LBS</title>
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
                <h2 class="login-title text-center">Mot de passe oublié</h2>
                <p class="login-subtitle text-center">Saisissez votre e-mail pour recevoir un lien de réinitialisation</p>

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

                <?php if (!$success): ?>
                <form method="post" class="login-form" autocomplete="on">
                    <?= csrf_input() ?>
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               placeholder="Votre adresse email" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login-submit">
                        <i class="fas fa-paper-plane"></i>
                        <span>Envoyer le lien</span>
                    </button>
                </form>
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
