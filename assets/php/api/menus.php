<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$pdo = getDB();
$where  = [];
$params = [];

// Filtre Prix Max
if (!empty($_GET['prix_max']) && is_numeric($_GET['prix_max'])) {
    $where[]  = 'm.prix_base <= ?';
    $params[] = (float)$_GET['prix_max'];
}
// Filtre Prix Min
if (!empty($_GET['prix_min']) && is_numeric($_GET['prix_min'])) {
    $where[]  = 'm.prix_base >= ?';
    $params[] = (float)$_GET['prix_min'];
}

// Filtre Thème
if (!empty($_GET['theme']) && is_numeric($_GET['theme'])) {
    $where[]  = 'm.theme_id = ?';
    $params[] = (int)$_GET['theme'];
}

// Filtre Régime
if (!empty($_GET['regime']) && is_numeric($_GET['regime'])) {
    $where[]  = 'm.regime_id = ?';
    $params[] = (int)$_GET['regime'];
}

// Nombre de personnes
if (!empty($_GET['personnes']) && is_numeric($_GET['personnes'])) {
    $where[]  = 'm.nombre_personne_min <= ?';
    $params[] = (int)$_GET['personnes'];
}

$sql = '
    SELECT m.menu_id, m.titre, m.description, m.nombre_personne_min,
        m.prix_base, m.stock_disponible,
        t.libelle AS theme,
        r.libelle AS regime,
        (SELECT p.image_path 
         FROM compose_menu cm 
         JOIN plat p ON cm.plat_id = p.plat_id 
         WHERE cm.menu_id = m.menu_id 
           AND p.type = \'plat\' 
         LIMIT 1) AS image_path
    FROM menu m
    LEFT JOIN theme t ON m.theme_id = t.theme_id
    LEFT JOIN regime r ON m.regime_id = r.regime_id
';

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);


$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($menus);