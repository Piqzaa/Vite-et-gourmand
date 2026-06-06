<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/MongoLogger.php';

$logger = new MongoLogger('login_logs');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../includes/session.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

// Vérifie le token CSRF
if (!validateCsrfToken($csrfToken)) {
    $logger->log('echec_connexion', ['email' => $email, 'raison' => 'csrf_invalide']);
    header('Location: ' . BASE_URL . '/index.php?page=login&error=csrf_invalide');
    exit;
}

if (empty($email) || empty($password)) {
    $logger->log('echec_connexion', ['email' => $email, 'raison' => 'champs_vides']);
    header('Location: ' . BASE_URL . '/index.php?page=login&error=champs_vides');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// password_verify compare le mdp saisi avec le hash en BDD
if (!$user || !password_verify($password, $user['password'])) {
    $logger->log('echec_connexion', ['email' => $email, 'raison' => 'identifiants_invalides']);
    header('Location: ' . BASE_URL . '/index.php?page=login&error=identifiants_invalides');
    exit;
}

if (isset($user['actif']) && !$user['actif']) {
    $logger->log('echec_connexion', ['email' => $email, 'raison' => 'compte_desactive']);
    header('Location: ' . BASE_URL . '/index.php?page=login&error=compte_desactive');
    exit;
}

// Connexion OK — on stocke les infos en session
$_SESSION['user_id']      = $user['utilisateur_id'];
$_SESSION['user_nom']     = $user['nom'];
$_SESSION['user_prenom']  = $user['prenom'];
$_SESSION['user_role']    = $user['role'];
$_SESSION['user_email']   = $user['email'];
$_SESSION['user_gsm']     = $user['gsm'];
$_SESSION['user_adresse'] = $user['adresse_postale'];
$_SESSION['user_ville']   = $user['ville'];

$logger->log('succes_connexion', ['user_id' => $user['utilisateur_id'], 'email' => $email, 'role' => $user['role']]);

// Régénère le token CSRF après connexion (sécurité)
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Redirection selon le rôle
if ($user['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/espace-admin.php');
} elseif ($user['role'] === 'employe') {
    header('Location: ' . BASE_URL . '/espace-employe.php');
} else {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php');
}
exit;