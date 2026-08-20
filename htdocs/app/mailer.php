<?php
/**
 * Helper d'envoi d'e-mails applicatifs (bienvenue, notifications de soumission/validation).
 * S'appuie sur l'infrastructure PHPMailer + Mailtrap déjà en place dans app/mail_config.php.
 */
require_once __DIR__ . '/mail_config.php';

$mailer_composer_autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($mailer_composer_autoload)) {
    require_once $mailer_composer_autoload;
}

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Envoie un e-mail HTML via PHPMailer/Mailtrap. Ne lève jamais d'exception :
 * en cas d'échec (config manquante, erreur SMTP...), log l'erreur et retourne false,
 * pour ne jamais bloquer le flux utilisateur qui a déclenché l'envoi.
 */
function send_app_mail(string $to, string $subject, string $htmlBody, ?string $toName = null): bool
{
    if (!class_exists(PHPMailer::class)) {
        error_log('send_app_mail: PHPMailer non installé (vendor/autoload.php introuvable).');
        return false;
    }

    $settings = mailtrap_settings();
    if ($settings['user'] === '' || $settings['pass'] === '') {
        error_log('send_app_mail: configuration Mailtrap manquante (MAILTRAP_USER / MAILTRAP_PASS dans .env).');
        return false;
    }

    try {
        $mail = new PHPMailer(true);
        configure_phpmailer_smtp($mail, $settings);
        $mail->setFrom($settings['from_email'], $settings['from_name']);
        $mail->addAddress($to, $toName ?? '');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $htmlBody)));
        $mail->send();
        return true;
    } catch (MailException $e) {
        error_log('send_app_mail: échec envoi vers ' . $to . ' — ' . $e->getMessage());
        return false;
    }
}

/**
 * URL absolue (avec domaine) pour les liens cliqués depuis un client e-mail.
 * url_to() seul ne suffit pas ici : BASE_URL vaut '/' en production (chemin relatif,
 * correct pour les liens internes aux pages, mais invalide dans un e-mail où il n'y a
 * pas de page d'origine pour le résoudre).
 */
function mail_url_to(string $path = ''): string
{
    $siteUrl = env_value('SITE_URL', 'http://localhost/M-moire_officielle-/htdocs');
    return rtrim($siteUrl, '/') . '/' . ltrim($path, '/');
}

/** Enveloppe HTML commune (en-tête bleu nuit LBS, corps, pied de page) pour tous les templates. */
function render_email_template(string $title, string $bodyHtml): string
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"></head>'
        . '<body style="margin:0;padding:0;background:#f1f3f5;font-family:Segoe UI,Arial,sans-serif;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f3f5;padding:24px 0;">'
        . '<tr><td align="center">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">'
        . '<tr><td style="background:#3B5A85;padding:24px 32px;">'
        . '<span style="color:#ffffff;font-size:20px;font-weight:700;">SEIC — Lomé Business School</span>'
        . '</td></tr>'
        . '<tr><td style="padding:32px;color:#1a1a1a;font-size:15px;line-height:1.6;">'
        . '<h2 style="margin:0 0 16px;color:#3B5A85;font-size:18px;">' . $safeTitle . '</h2>'
        . $bodyHtml
        . '</td></tr>'
        . '<tr><td style="background:#f8f9fa;padding:16px 32px;color:#6a6a6a;font-size:12px;border-top:1px solid #e2e8f0;">'
        . 'Plateforme SEIC — Lomé Business School. Ceci est un e-mail automatique, merci de ne pas y répondre.'
        . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

/** Bouton HTML réutilisable dans les templates. */
function email_button(string $url, string $label): string
{
    return '<p><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" '
        . 'style="display:inline-block;background:#3B5A85;color:#ffffff;text-decoration:none;'
        . 'padding:10px 22px;border-radius:8px;font-weight:600;">'
        . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a></p>';
}

/** Comptes organisateurs (nom, email) à notifier lors d'une nouvelle soumission. */
function organizer_emails(PDO $pdo): array
{
    return $pdo->query("SELECT nom, email FROM utilisateurs WHERE role = 'organisateur'")->fetchAll(PDO::FETCH_ASSOC);
}

/** E-mail de bienvenue envoyé à l'inscription d'un nouveau compte. */
function send_welcome_email(string $toEmail, string $toName): bool
{
    $body = '<p>Bonjour ' . htmlspecialchars($toName, ENT_QUOTES, 'UTF-8') . ',</p>'
        . '<p>Votre compte sur la plateforme <strong>SEIC LBS</strong> a bien été créé. Bienvenue !</p>'
        . '<p>Vous pouvez dès à présent vous connecter et accéder à votre espace.</p>'
        . email_button(mail_url_to('login.php'), 'Se connecter');

    return send_app_mail(
        $toEmail,
        'Bienvenue sur la plateforme SEIC LBS',
        render_email_template('Bienvenue sur SEIC LBS', $body),
        $toName
    );
}

/**
 * Notifie tous les comptes organisateurs qu'un étudiant vient de soumettre
 * un projet, un rapport ou une présentation PowerPoint.
 */
function send_submission_notification_to_organizers(
    PDO $pdo,
    string $type,
    string $studentName,
    string $projectTitle,
    string $link
): void {
    $labels = [
        'projet' => 'un projet',
        'rapport' => 'un rapport',
        'powerpoint' => 'une présentation PowerPoint',
    ];
    $label = $labels[$type] ?? 'une soumission';

    $body = '<p>Bonjour,</p>'
        . '<p><strong>' . htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') . '</strong> vient de soumettre ' . $label
        . ' pour le projet <strong>' . htmlspecialchars($projectTitle, ENT_QUOTES, 'UTF-8') . '</strong>.</p>'
        . email_button($link, 'Consulter la soumission');

    $subject = 'Nouvelle soumission (' . $label . ') — ' . $projectTitle;
    $html = render_email_template('Nouvelle soumission étudiante', $body);

    foreach (organizer_emails($pdo) as $organizer) {
        send_app_mail($organizer['email'], $subject, $html, $organizer['nom']);
    }
}

/**
 * Notifie l'étudiant que sa soumission (rapport ou présentation) vient d'être
 * validée ou refusée par l'organisateur.
 */
function send_validation_notification_to_student(
    string $toEmail,
    string $toName,
    string $type,
    string $projectTitle,
    string $status,
    string $link,
    ?string $comment = null
): bool {
    $labels = ['rapport' => 'rapport', 'powerpoint' => 'présentation PowerPoint'];
    $label = $labels[$type] ?? 'soumission';
    $isValid = $status === 'valide';

    $body = '<p>Bonjour ' . htmlspecialchars($toName, ENT_QUOTES, 'UTF-8') . ',</p>'
        . '<p>Votre ' . $label . ' pour le projet <strong>' . htmlspecialchars($projectTitle, ENT_QUOTES, 'UTF-8') . '</strong> a été '
        . ($isValid
            ? '<strong style="color:#06A77D;">validé(e)</strong>'
            : '<strong style="color:#5A1F26;">refusé(e)</strong>')
        . ' par l\'organisateur.</p>'
        . ($comment ? '<p><em>Commentaire : ' . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . '</em></p>' : '')
        . email_button($link, 'Voir ma soumission');

    $subject = ucfirst($label) . ' ' . ($isValid ? 'validé' : 'refusé') . ' — ' . $projectTitle;

    return send_app_mail(
        $toEmail,
        $subject,
        render_email_template('Mise à jour de votre soumission', $body),
        $toName
    );
}
