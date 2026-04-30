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

$pdo     = getDB();
$horaires = $_POST['horaire'] ?? [];

if (empty($horaires)) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=donnees_invalides#horaires');
    exit;
}

$stmt = $pdo->prepare('
    UPDATE horaire 
    SET heure_ouverture = ?, heure_fermeture = ? 
    WHERE horaire_id = ?
');

foreach ($horaires as $horaireId => $valeurs) {
    $ouverture  = $valeurs['ouverture'] ?? null;
    $fermeture  = $valeurs['fermeture'] ?? null;

    if ($ouverture && $fermeture) {
        $stmt->execute([$ouverture, $fermeture, (int)$horaireId]);
    }
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=horaires_mis_a_jour#horaires');
exit;