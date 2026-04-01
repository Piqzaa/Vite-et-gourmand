<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected()) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/commande.php');
    exit;
}

$pdo = getDB();

// Récupération et nettoyage des données POST
$menuId         = (int)($_POST['menu_id'] ?? 0);
$nbPersonnes    = (int)($_POST['nb_personnes'] ?? 0);
$adresse        = trim($_POST['adresse_livraison'] ?? '');
$ville          = trim($_POST['ville_livraison'] ?? '');
$datePrestation = $_POST['date_livraison'] ?? '';
$heure          = $_POST['heure_livraison'] ?? '';

// Validation basique
if (!$menuId || !$nbPersonnes || !$adresse || !$ville || !$datePrestation || !$heure) {
    header('Location: ' . BASE_URL . '/commande.php?error=champs_manquants');
    exit;
}

// Récupère le menu
$stmt = $pdo->prepare('
    SELECT prix_base, nombre_personne_min, stock_disponible 
    FROM menu 
    WHERE menu_id = ?
');
$stmt->execute([$menuId]);
$menu = $stmt->fetch();

if (!$menu) {
    header('Location: ' . BASE_URL . '/commande.php?error=menu_introuvable');
    exit;
}

// Vérifie stock
if ($menu['stock_disponible'] <= 0) {
    header('Location: ' . BASE_URL . '/commande.php?error=stock_epuise');
    exit;
}

// Vérifie nombre de personnes minimum
if ($nbPersonnes < $menu['nombre_personne_min']) {
    header('Location: ' . BASE_URL . '/commande.php?error=nb_personnes_insuffisant');
    exit;
}

// ─── Calcul prix (même logique que le JS) ───────────────────
$prixParPers = $menu['prix_base'] / $menu['nombre_personne_min'];
$prixMenu    = $prixParPers * $nbPersonnes;
$reduction   = 0;

if ($nbPersonnes >= $menu['nombre_personne_min'] + 5) {
    $reduction = $prixMenu * 0.10;
    $prixMenu -= $reduction;
}

$estHorsBordeaux = (stripos($ville, 'bordeaux') === false) ? 1 : 0;
$prixLivraison   = $estHorsBordeaux ? 5.00 : 0.00;
$prixTotal       = round($prixMenu + $prixLivraison, 2);

// ─── Insertion commande ──────────────────────────────────────
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('
        INSERT INTO commande 
            (date_commande, date_prestation, heure_prestation, adresse_livraison,
            est_hors_bordeaux, nombre_personnes, prix_total_ttc, statut,
            pret_materiel, materiel_rendu, utilisateur_id, menu_id)
        VALUES 
            (NOW(), ?, ?, ?, ?, ?, ?, "en attente", 0, 0, ?, ?)
    ');
    $stmt->execute([
        $datePrestation,
        $heure,
        $adresse . ', ' . $ville,
        $estHorsBordeaux,
        $nbPersonnes,
        $prixTotal,
        getUserId(),
        $menuId
    ]);
    $commandeId = $pdo->lastInsertId();

    // Insertion suivi initial
    $stmtSuivi = $pdo->prepare('
        INSERT INTO suivi_commande (commande_id, statut, commentaire)
        VALUES (?, "en attente", "Commande reçue")
    ');
    $stmtSuivi->execute([$commandeId]);

    // Décrémente le stock
    $pdo->prepare('
        UPDATE menu SET stock_disponible = stock_disponible - 1 
        WHERE menu_id = ?
    ')->execute([$menuId]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/commande.php?error=erreur_serveur');
    exit;
}

// TODO : PHPMailer — mail de confirmation

header('Location: ' . BASE_URL . '/espace-utilisateur.php?commande=' . $commandeId . '&success=1');
exit;