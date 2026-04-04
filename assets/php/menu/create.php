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

$pdo = getDB();

$titre       = trim($_POST['titre'] ?? '');
$description = trim($_POST['description'] ?? '');
$prix        = (float)($_POST['prix_base'] ?? 0);
$personnes   = (int)($_POST['nombre_personne_min'] ?? 0);
$stock       = (int)($_POST['stock_disponible'] ?? 0);
$themeId     = (int)($_POST['theme_id'] ?? 0);
$regimeId    = (int)($_POST['regime_id'] ?? 0);
$conditions  = trim($_POST['conditions_particulieres'] ?? '');
$plats       = $_POST['plats'] ?? [];

if (!$titre || !$description || !$prix || !$personnes || !$themeId || !$regimeId) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=champs_manquants#menus');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('
        INSERT INTO menu (titre, description, prix_base, nombre_personne_min, 
                         stock_disponible, theme_id, regime_id, conditions_particulieres)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$titre, $description, $prix, $personnes, $stock, $themeId, $regimeId, $conditions]);
    $menuId = $pdo->lastInsertId();

    // Association des plats
    if (!empty($plats)) {
        $stmtPlat = $pdo->prepare('INSERT INTO compose_menu (menu_id, plat_id) VALUES (?, ?)');
        foreach ($plats as $platId) {
            $stmtPlat->execute([$menuId, (int)$platId]);
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/espace-employe.php?error=erreur_serveur#menus');
    exit;
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=menu_cree#menus');
exit;