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

$pdo    = getDB();
$platId = (int)($_POST['plat_id'] ?? 0);

if (!$platId) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=plat_invalide#menus');
    exit;
}

$pdo->prepare('DELETE FROM plat WHERE plat_id = ?')->execute([$platId]);

header('Location: ' . BASE_URL . '/espace-employe.php?success=plat_supprime#menus');
exit;