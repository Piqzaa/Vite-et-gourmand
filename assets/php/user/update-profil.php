<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected()) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php');
    exit;
}

$pdo    = getDB();
$userId = getUserId();

$prenom  = trim($_POST['prenom'] ?? '');
$nom     = trim($_POST['nom'] ?? '');
$email   = trim($_POST['email'] ?? '');
$gsm     = trim($_POST['gsm'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$ville   = trim($_POST['ville'] ?? '');

$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

// Validation basique
if (!$prenom || !$nom || !$email || !$gsm) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=champs_manquants#profil');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=email_invalide#profil');
    exit;
}

// Vérifie que l'email n'est pas déjà pris par quelqu'un d'autre
$stmt = $pdo->prepare('SELECT utilisateur_id FROM utilisateur WHERE email = ? AND utilisateur_id != ?');
$stmt->execute([$email, $userId]);
if ($stmt->fetch()) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=email_pris#profil');
    exit;
}

// Mise à jour sans mot de passe
$sql    = 'UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, gsm = ?, adresse_postale = ?, ville = ? WHERE utilisateur_id = ?';
$params = [$prenom, $nom, $email, $gsm, $adresse, $ville, $userId];

// Mise à jour avec mot de passe si fourni
if (!empty($password)) {
    // Vérifie que les deux champs correspondent
    if ($password !== $passwordConfirm) {
        header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=password_mismatch#profil');
        exit;
    }

    // Validation force : 10 car min, 1 maj, 1 min, 1 chiffre, 1 spécial
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';
    if (!preg_match($passwordRegex, $password)) {
        header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=password_faible#profil');
        exit;
    }

    $sql    = 'UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, gsm = ?, adresse_postale = ?, ville = ?, password = ? WHERE utilisateur_id = ?';
    $params = [$prenom, $nom, $email, $gsm, $adresse, $ville, password_hash($password, PASSWORD_BCRYPT), $userId];
} else {
    $sql    = 'UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, gsm = ?, adresse_postale = ?, ville = ? WHERE utilisateur_id = ?';
    $params = [$prenom, $nom, $email, $gsm, $adresse, $ville, $userId];
}

$pdo->prepare($sql)->execute($params);

// Met à jour la session avec les nouvelles infos
$_SESSION['user_prenom'] = $prenom;
$_SESSION['user_nom']    = $nom;
$_SESSION['user_email']  = $email;
$_SESSION['user_gsm']    = $gsm;
$_SESSION['user_ville']  = $ville;

header('Location: ' . BASE_URL . '/espace-utilisateur.php?success=profil_mis_a_jour#profil');
exit;