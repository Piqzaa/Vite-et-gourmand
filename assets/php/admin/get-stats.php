<?php
require_once '../config/db.php';
$pdo = getDB();

$menu_id = $_GET['menu_id'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

$params = [];
$where = ["statut NOT IN ('annulée')"];

if ($menu_id) {
    $where[] = "c.menu_id = ?";
    $params[] = $menu_id;
}
if ($date_debut) {
    $where[] = "c.date_commande >= ?";
    $params[] = $date_debut;
}
if ($date_fin) {
    $where[] = "c.date_commande <= ?";
    $params[] = $date_fin;
}

$whereClause = implode(" AND ", $where);

// 1. Stats Globales (les cartes)
$stmt = $pdo->prepare("SELECT COUNT(*) as nb, SUM(prix_total_ttc) as ca, AVG(prix_total_ttc) as moy 
                       FROM commande c 
                       WHERE $whereClause");
$stmt->execute($params);
$totals = $stmt->fetch();
// 2. Stats par Menu (le graphique)
$stmtGraph = $pdo->prepare("SELECT m.titre, COUNT(c.commande_id) as nb, SUM(c.prix_total_ttc) as ca 
                            FROM commande c JOIN menu m ON c.menu_id = m.menu_id 
                            WHERE $whereClause GROUP BY m.menu_id");
$stmtGraph->execute($params);
$graph = $stmtGraph->fetchAll();

echo json_encode([
    'totals' => $totals,
    'chart' => [
        'labels' => array_column($graph, 'titre'),
        'commandes' => array_column($graph, 'nb'),
        'ca' => array_column($graph, 'ca'),
    ]
]);