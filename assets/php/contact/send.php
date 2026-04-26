<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

$email   = trim($_POST['email'] ?? '');
$titre   = trim($_POST['titre'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$email || !$titre || !$message) {
    header('Location: ' . BASE_URL . '/contact.php?error=champs_manquants');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . BASE_URL . '/contact.php?error=email_invalide');
    exit;
}

$to      = 'contact@viteetgourmand.fr';
$subject = '[Contact] ' . $titre;
$body    = "Nouveau message depuis le formulaire de contact.\n\n"
         . "De : $email\n"
         . "Titre : $titre\n\n"
         . "Message :\n$message";
$headers = "From: contact@viteetgourmand.fr\r\n"
         . "Reply-To: $email\r\n"
         . "Content-Type: text/plain; charset=UTF-8";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    header('Location: ' . BASE_URL . '/contact.php?success=message_envoye');
} else {
    header('Location: ' . BASE_URL . '/contact.php?error=envoi_impossible');
}
exit;