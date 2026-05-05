<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected() || !in_array(getUserRole(), ['employe', 'admin'])) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/espace-employe.php');
    exit;
}

// Vérification CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrfToken)) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=csrf_invalide');
    exit;
}

$pdo    = getDB();
$menuId = (int)($_POST['menu_id'] ?? 0);

if (!$menuId) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=menu_invalide#menus');
    exit;
}

// Vérifie qu'aucune commande active n'est liée à ce menu
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM commande 
    WHERE menu_id = ? AND statut NOT IN ('annulée', 'terminée')
");
$stmt->execute([$menuId]);
if ($stmt->fetchColumn() > 0) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=menu_commande_active#menus');
    exit;
}

$pdo->prepare('DELETE FROM menu WHERE menu_id = ?')->execute([$menuId]);

header('Location: ' . BASE_URL . '/espace-employe.php?success=menu_supprime#menus');
exit;