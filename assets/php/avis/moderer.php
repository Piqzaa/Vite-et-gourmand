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
$avisId = (int)($_POST['avis_id'] ?? 0);
$action = trim($_POST['action'] ?? '');

if (!$avisId || !in_array($action, ['valider', 'refuser'])) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=donnees_invalides#avis');
    exit;
}

if ($action === 'valider') {
    $pdo->prepare('UPDATE avis SET est_valide = 1 WHERE avis_id = ?')->execute([$avisId]);
} else {
    // Refuser = supprimer l'avis
    $pdo->prepare('DELETE FROM avis WHERE avis_id = ?')->execute([$avisId]);
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=avis_modere#avis');
exit;