<?php
/**
 * Test SMTP Mailtrap — usage CLI :
 * php database/test_mailtrap_smtp.php destinataire@example.com
 */
require dirname(__DIR__) . '/htdocs/bootstrap.php';
require dirname(__DIR__) . '/htdocs/app/mail_config.php';
require dirname(__DIR__) . '/htdocs/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

$to = $argv[1] ?? 'sibikhalil06@gmail.com';
$settings = mailtrap_settings();

echo "Mode: {$settings['mode']}\n";
echo "Host: {$settings['host']}:{$settings['port']}\n";
echo "User: {$settings['user']}\n";
echo "From: {$settings['from_email']}\n";
echo "To: {$to}\n";

if ($settings['user'] === '' || $settings['pass'] === '') {
    fwrite(STDERR, "ERREUR: renseignez MAILTRAP_USER et MAILTRAP_PASS dans .env\n");
    exit(1);
}

try {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = static function ($str, $level) {
        echo "[debug {$level}] {$str}\n";
    };
    configure_phpmailer_smtp($mail, $settings);
    $mail->setFrom($settings['from_email'], $settings['from_name']);
    $mail->addAddress($to);
    $mail->Subject = 'Test SMTP SEIC LBS';
    $mail->Body = 'Test envoi mot de passe oublié (sandbox local).';
    $mail->send();
    echo "OK: e-mail envoye vers Mailtrap ({$settings['mode']}).\n";
} catch (MailException $e) {
    fwrite(STDERR, 'ERREUR: ' . mailtrap_smtp_error_message($e->getMessage(), $settings) . PHP_EOL);
    exit(1);
}
