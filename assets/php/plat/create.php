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

$pdo      = getDB();
$libelle  = trim($_POST['libelle'] ?? '');
$type     = trim($_POST['type'] ?? '');
$allergenes = $_POST['allergenes'] ?? [];

if (!$libelle || !in_array($type, ['entrée', 'plat', 'dessert'])) {
    header('Location: ' . BASE_URL . '/plat-create.php?error=champs_manquants');
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->prepare('INSERT INTO plat (libelle, type) VALUES (?, ?)')->execute([$libelle, $type]);
    $platId = $pdo->lastInsertId();

    if (!empty($allergenes)) {
        $stmt = $pdo->prepare('INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (?, ?)');
        foreach ($allergenes as $allergeneId) {
            $stmt->execute([$platId, (int)$allergeneId]);
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/plat-create.php?error=erreur_serveur');
    exit;
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=plat_cree#menus');
exit;