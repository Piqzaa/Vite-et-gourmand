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
            $mail->Host = getenv('SMTP_HOST') ?: 'mailpit';
            $mail->Port = (int)(getenv('SMTP_PORT') ?: 1025);
            $mail->CharSet = 'UTF-8';
            $smtpUser = getenv('SMTP_USER');
            if (!empty($smtpUser)) {
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUser;
                $mail->Password   = getenv('SMTP_PASS');
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPAuth = false;
            }

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
