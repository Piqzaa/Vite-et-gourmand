<?php


declare(strict_types=1);

session_start();

require __DIR__ . '/../config/db.php';
$pdo = getDB();


// ── 1. Méthode ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/inscription.php');
    exit;
}

// ── 2. Récupération & nettoyage des champs ────────────────────────────────────
$prenom   = trim($_POST['prenom']   ?? '');
$nom      = trim($_POST['nom']      ?? '');
$email    = trim($_POST['email']    ?? '');
$gsm      = trim($_POST['gsm']      ?? '');
$adresse  = trim($_POST['adresse']  ?? '');
$ville    = trim($_POST['ville']    ?? '');
$password = $_POST['password']         ?? '';
$confirm  = $_POST['password_confirm'] ?? '';
$cgv      = isset($_POST['cgv']);

// ── 3. Validation ─────────────────────────────────────────────────────────────
$errors = [];

if (empty($prenom))  $errors[] = 'Le prénom est obligatoire.';
if (empty($nom))     $errors[] = 'Le nom est obligatoire.';
if (empty($adresse)) $errors[] = "L'adresse est obligatoire.";
if (empty($ville))   $errors[] = 'La ville est obligatoire.';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Adresse email invalide.';
}

// GSM : on accepte les formats FR courants (10 chiffres, espaces/tirets optionnels)
if (!preg_match('/^(\+33|0)[1-9]([\s.\-]?\d{2}){4}$/', $gsm)) {
    $errors[] = 'Numéro de téléphone invalide.';
}

// Règles mot de passe : 10 car min, 1 maj, 1 min, 1 chiffre, 1 spécial
if (strlen($password) < 10) {
    $errors[] = 'Le mot de passe doit contenir au moins 10 caractères.';
} elseif (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Le mot de passe doit contenir au moins une majuscule.';
} elseif (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Le mot de passe doit contenir au moins une minuscule.';
} elseif (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Le mot de passe doit contenir au moins un chiffre.';
} elseif (!preg_match('/[\W_]/', $password)) {
    $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
}

if ($password !== $confirm) {
    $errors[] = 'Les mots de passe ne correspondent pas.';
}

if (!$cgv) {
    $errors[] = 'Vous devez accepter les conditions générales de vente.';
}

// ── 4. Si erreurs de validation → retour formulaire ───────────────────────────
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old']    = compact('prenom', 'nom', 'email', 'gsm', 'adresse', 'ville');
    header('Location: ' . BASE_URL . '/inscription.php');
    exit;
}

// ── 5. Email déjà utilisé ? ───────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT utilisateur_id FROM utilisateur WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);

if ($stmt->fetch()) {
    $_SESSION['register_errors'] = ['Cette adresse email est déjà associée à un compte.'];
    $_SESSION['register_old']    = compact('prenom', 'nom', 'email', 'gsm', 'adresse', 'ville');
    header('Location: ' . BASE_URL . '/inscription.php');
    exit;
}

// ── 6. Hash du mot de passe & insertion ───────────────────────────────────────
$hash = password_hash($password, PASSWORD_BCRYPT);

$insert = $pdo->prepare('
    INSERT INTO utilisateur (prenom, nom, email, gsm, adresse_postale, ville, password, role)
    VALUES (:prenom, :nom, :email, :gsm, :adresse, :ville, :password, :role)
');

$insert->execute([
    ':prenom'   => $prenom,
    ':nom'      => $nom,
    ':email'    => $email,
    ':gsm'      => $gsm,
    ':adresse'  => $adresse,
    ':ville'    => $ville,
    ':password' => $hash,
    ':role'     => 'utilisateur',
]);

// ── 7. Connexion automatique post-inscription ─────────────────────────────────
$_SESSION['user'] = [
    'id'     => (int) $pdo->lastInsertId(),
    'prenom' => $prenom,
    'nom'    => $nom,
    'email'  => $email,
    'role'   => 'utilisateur',
];

$_SESSION['flash_success'] = 'Bienvenue ' . htmlspecialchars($prenom) . ' ! Votre compte a bien été créé.';

header('Location: ' . BASE_URL . '/');
exit;
