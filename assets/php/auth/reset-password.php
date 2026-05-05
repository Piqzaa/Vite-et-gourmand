<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo      = getDB();
$token    = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['password_confirm'] ?? '';

if (!$token || !$password || !$confirm) {
    header('Location: ' . BASE_URL . '/reset-password.php?token=' . $token . '&error=champs_manquants');
    exit;
}

// Vérifie token encore valide
$stmt = $pdo->prepare('
    SELECT utilisateur_id FROM utilisateur 
    WHERE reset_token = ? AND reset_token_expire > NOW()
');
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ' . BASE_URL . '/connexion.php?error=token_expire');
    exit;
}

// Validation mot de passe
$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';
if (!preg_match($passwordRegex, $password)) {
    header('Location: ' . BASE_URL . '/reset-password.php?token=' . $token . '&error=password_faible');
    exit;
}

if ($password !== $confirm) {
    header('Location: ' . BASE_URL . '/reset-password.php?token=' . $token . '&error=password_mismatch');
    exit;
}

// Met à jour le mot de passe et invalide le token
$pdo->prepare('
    UPDATE utilisateur 
    SET password = ?, reset_token = NULL, reset_token_expire = NULL 
    WHERE utilisateur_id = ?
')->execute([password_hash($password, PASSWORD_BCRYPT), $user['utilisateur_id']]);

header('Location: ' . BASE_URL . '/connexion.php?success=password_reinitialise');
exit;