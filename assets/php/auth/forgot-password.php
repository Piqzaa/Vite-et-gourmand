<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . BASE_URL . '/connexion.php?error=email_invalide#forgot');
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare('SELECT utilisateur_id, prenom FROM utilisateur WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ' . BASE_URL . '/connexion.php?success=reset_envoye#forgot');
    exit;
}

// Génère un token sécurisé valable 1h

date_default_timezone_set('Europe/Paris');
$token  = bin2hex(random_bytes(32));
$expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

$pdo->prepare('UPDATE utilisateur SET reset_token = ?, reset_token_expire = ? WHERE utilisateur_id = ?')
    ->execute([$token, $expire, $user['utilisateur_id']]);

$resetUrl = BASE_URL . '/reset-password.php?token=' . $token;

mailResetPassword($email, $user['prenom'], $resetUrl);

header('Location: ' . BASE_URL . '/connexion.php?success=reset_envoye#forgot');
exit;