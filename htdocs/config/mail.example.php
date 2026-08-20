<?php
/**
 * Configuration e-mail SMTP — modèle à copier vers mail.php
 *
 * cp config/mail.example.php config/mail.php
 * Puis éditez les identifiants SMTP réels (Gmail, Mailtrap, ou autre).
 *
 * Ce fichier est optionnel : s'il n'existe pas, app/mail_config.php se replie
 * automatiquement sur les variables MAILTRAP_* du fichier .env.
 */
if (defined('MAIL_CONFIG_LOADED')) {
    return;
}
define('MAIL_CONFIG_LOADED', true);

if (ENV === 'local') {
    define('SMTP_MODE', 'sandbox');
    define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
    define('SMTP_PORT', 2525);
    define('SMTP_ENCRYPTION', 'tls');
    define('SMTP_USER', 'CHANGE_ME');
    define('SMTP_PASS', 'CHANGE_ME');
    define('SMTP_FROM_EMAIL', 'noreply@seic-lbs.local');
    define('SMTP_FROM_NAME', 'SEIC LBS');
} else {
    // Exemple Gmail SMTP (mot de passe d'application à 16 caractères,
    // généré sur myaccount.google.com → Sécurité → Validation en 2 étapes
    // → Mots de passe des applications).
    define('SMTP_MODE', 'live');
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', 587);
    define('SMTP_ENCRYPTION', 'tls');
    define('SMTP_USER', 'votre-adresse@gmail.com');
    define('SMTP_PASS', 'CHANGE_ME');
    define('SMTP_FROM_EMAIL', 'votre-adresse@gmail.com');
    define('SMTP_FROM_NAME', 'SEIC LBS');
}
