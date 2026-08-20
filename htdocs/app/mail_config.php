<?php
/**
 * Configuration e-mail SMTP (sandbox local via Mailtrap / live production).
 *
 * Deux sources possibles, dans cet ordre de priorité :
 * 1. config/mail.php (gitignored, comme config/db.php) si ce fichier existe :
 *    valeurs SMTP codées en dur, aucun .env nécessaire.
 * 2. Sinon, repli sur les variables MAILTRAP_* lues depuis .env (ancien
 *    fonctionnement, toujours actif en local si config/mail.php est absent).
 */
require_once __DIR__ . '/../config/config.php';

$mail_config_file = __DIR__ . '/../config/mail.php';
if (is_file($mail_config_file)) {
    require_once $mail_config_file;
}

function mail_mode(): string
{
    if (defined('SMTP_MODE')) {
        return SMTP_MODE;
    }
    $mode = strtolower((string) env_value('MAIL_MODE', APP_ENV === 'production' ? 'live' : 'sandbox'));
    return in_array($mode, ['sandbox', 'live'], true) ? $mode : 'sandbox';
}

function mailtrap_settings(): array
{
    if (defined('SMTP_HOST')) {
        return [
            'mode' => mail_mode(),
            'host' => SMTP_HOST,
            'port' => (int) SMTP_PORT,
            'user' => SMTP_USER,
            'pass' => SMTP_PASS,
            'from_email' => SMTP_FROM_EMAIL,
            'from_name' => SMTP_FROM_NAME,
            'encryption' => strtolower(SMTP_ENCRYPTION),
        ];
    }

    $isSandbox = mail_mode() === 'sandbox';

    return [
        'mode' => mail_mode(),
        'host' => env_value('MAILTRAP_HOST', $isSandbox ? 'sandbox.smtp.mailtrap.io' : 'live.smtp.mailtrap.io'),
        'port' => (int) env_value('MAILTRAP_PORT', $isSandbox ? 2525 : 587),
        'user' => env_value('MAILTRAP_USER', ''),
        'pass' => env_value('MAILTRAP_PASS', ''),
        'from_email' => env_value(
            'MAILTRAP_FROM_EMAIL',
            $isSandbox ? 'noreply@seic-lbs.local' : 'info@lomebs.com'
        ),
        'from_name' => env_value('MAILTRAP_FROM_NAME', 'SEIC LBS'),
        'encryption' => strtolower((string) env_value('MAILTRAP_ENCRYPTION', $isSandbox ? 'tls' : 'tls')),
    ];
}

function configure_phpmailer_smtp(PHPMailer\PHPMailer\PHPMailer $mail, array $settings): void
{
    $mail->isSMTP();
    $mail->Host = $settings['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['user'];
    $mail->Password = $settings['pass'];
    $mail->Port = $settings['port'];
    $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
    $mail->Timeout = 20;
    // Évite la signature "PHPMailer x.y.z" dans les en-têtes, un signal souvent
    // associé aux scripts d'envoi de masse par les filtres anti-spam.
    $mail->XMailer = ' ';
    $mail->addReplyTo($settings['from_email'], $settings['from_name']);

    $encryption = $settings['encryption'];
    if ($encryption === 'ssl' || $encryption === 'smtps') {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($encryption === 'tls' || $encryption === 'starttls') {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } elseif ($settings['port'] === 465) {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif (in_array($settings['port'], [587, 2525, 25], true)) {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    }
}

function mailtrap_smtp_error_message(string $smtpError, array $settings): string
{
    $isGmail = stripos($settings['host'], 'gmail') !== false;

    if (stripos($smtpError, 'not allowed') !== false || stripos($smtpError, '550') !== false) {
        if ($isGmail) {
            return 'Adresse expéditeur refusée par Gmail. MAILTRAP_FROM_EMAIL doit être identique (ou un alias autorisé) à l\'adresse Gmail utilisée pour MAILTRAP_USER.';
        }
        return 'Le domaine expéditeur « ' . $settings['from_email']
            . ' » n\'est pas autorisé. En local, utilisez MAIL_MODE=sandbox dans .env.';
    }

    if (stripos($smtpError, 'authenticate') !== false || stripos($smtpError, '535') !== false) {
        if ($isGmail) {
            return 'Authentification Gmail refusée. Utilisez un mot de passe d\'application Gmail (16 caractères, généré '
                . 'via myaccount.google.com → Sécurité → Validation en 2 étapes → Mots de passe des applications), '
                . 'pas votre mot de passe Gmail normal.';
        }
        if ($settings['mode'] === 'sandbox') {
            return 'Authentification sandbox refusée. Copiez Username et Password depuis Mailtrap → Email Testing → Inboxes → SMTP Settings.';
        }
        return 'Authentification SMTP refusée. En production : MAILTRAP_USER=api et MAILTRAP_PASS=votre_jeton_api.';
    }

    return 'Impossible d\'envoyer l\'e-mail : ' . $smtpError;
}

function password_reset_success_message(): string
{
    return 'E-mail envoyé.';
}
