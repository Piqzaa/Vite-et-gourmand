<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

// Vérification CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrfToken)) {
    header('Location: ' . BASE_URL . '/espace-admin.php?error=csrf_invalide#employes');
    exit;
}

$pdo       = getDB();
$employeId = (int)($_POST['employe_id'] ?? 0);
$actif     = (int)($_POST['actif'] ?? 0);

if (!$employeId) {
    header('Location: ' . BASE_URL . '/espace-admin.php?error=employe_invalide#employes');
    exit;
}

$pdo->prepare('UPDATE utilisateur SET actif = ? WHERE utilisateur_id = ? AND role = "employe"')
    ->execute([$actif, $employeId]);

header('Location: ' . BASE_URL . '/espace-admin.php?success=employe_mis_a_jour#employes');
exit;