<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo      = getDB();
// Vérification CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrfToken)) {
    header('Location: ' . BASE_URL . '/espace-admin.php?error=csrf_invalide#employes');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$nom      = trim($_POST['nom'] ?? '');
$prenom   = trim($_POST['prenom'] ?? '');

if (!$email || !$password || !$nom || !$prenom) {
    header('Location: ' . BASE_URL . '/espace-admin.php?error=champs_manquants#employes');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . BASE_URL . '/espace-admin.php?error=email_invalide#employes');
    exit;
}

// Vérifie que l'email n'est pas déjà utilisé
$stmt = $pdo->prepare('SELECT utilisateur_id FROM utilisateur WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header('Location: ' . BASE_URL . '/espace-admin.php?error=email_pris#employes');
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$pdo->prepare('
    INSERT INTO utilisateur (nom, prenom, email, password, role, actif, gsm, adresse_postale, ville)
    VALUES (?, ?, ?, ?, "employe", 1, "", "", "")
')->execute([$nom, $prenom, $email, $hash]);

// TODO : PHPMailer — notifier l'employé que son compte a été créé (sans le mdp)

header('Location: ' . BASE_URL . '/espace-admin.php?success=employe_cree#employes');
exit;