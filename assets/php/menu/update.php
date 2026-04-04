<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected() || !in_array(getUserRole(), ['employe', 'admin'])) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo    = getDB();
$menuId = (int)($_POST['menu_id'] ?? 0);

if (!$menuId) {
    header('Location: ' . BASE_URL . '/espace-employe.php');
    exit;
}

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
    header('Location: ' . BASE_URL . '/menu-edit.php?id=' . $menuId . '&error=champs_manquants');
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->prepare('
        UPDATE menu SET titre = ?, description = ?, prix_base = ?, 
        nombre_personne_min = ?, stock_disponible = ?, theme_id = ?, 
        regime_id = ?, conditions_particulieres = ?
        WHERE menu_id = ?
    ')->execute([$titre, $description, $prix, $personnes, $stock, $themeId, $regimeId, $conditions, $menuId]);

    // Resync des plats — on supprime tout et on réinsère
    $pdo->prepare('DELETE FROM compose_menu WHERE menu_id = ?')->execute([$menuId]);

    if (!empty($plats)) {
        $stmt = $pdo->prepare('INSERT INTO compose_menu (menu_id, plat_id) VALUES (?, ?)');
        foreach ($plats as $platId) {
            $stmt->execute([$menuId, (int)$platId]);
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/menu-edit.php?id=' . $menuId . '&error=erreur_serveur');
    exit;
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=menu_modifie#menus');
exit;