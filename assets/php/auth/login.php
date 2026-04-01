<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /connexion.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ' . BASE_URL . '/connexion.php?error=champs_vides');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// password_verify compare le mdp saisi avec le hash en BDD
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: ' . BASE_URL . '/connexion.php?error=identifiants_invalides');
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


// Redirection selon le rôle
if ($user['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/espace-admin.php');
} elseif ($user['role'] === 'employe') {
    header('Location: ' . BASE_URL . '/espace-employe.php');
} else {
    header('Location: /Vite-et-gourmand/espace-utilisateur.php');
}
exit;