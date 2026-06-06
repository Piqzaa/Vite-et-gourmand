<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    public function send(string $to, string $toName, string $subject, string $htmlBody): bool {
        $mail = new PHPMailer(true);
        try {
            // Configuration Serveur
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);
            $mail->CharSet    = 'UTF-8';

            // Destinataires
            $mail->setFrom(
                getenv('SMTP_FROM') ?: 'noreply@viteetgourmand.fr',
                getenv('SMTP_FROM_NAME') ?: 'Vite & Gourmand'
            );
            $mail->addAddress($to, $toName);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur MailService : {$mail->ErrorInfo}");
            return false;
        }
    }
}
