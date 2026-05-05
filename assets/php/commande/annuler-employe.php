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

$pdo          = getDB();
$commandeId   = (int)($_POST['commande_id'] ?? 0);
$motif        = trim($_POST['motif'] ?? '');
$modeContact  = trim($_POST['mode_contact'] ?? '');

if (!$commandeId || !$motif || !in_array($modeContact, ['gsm', 'mail'])) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=donnees_invalides#commandes');
    exit;
}

// Vérifie que la commande existe
$stmt = $pdo->prepare('SELECT commande_id, statut, menu_id FROM commande WHERE commande_id = ?');
$stmt->execute([$commandeId]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=commande_introuvable#commandes');
    exit;
}

if ($commande['statut'] === 'annulée' || $commande['statut'] === 'terminée') {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=annulation_impossible#commandes');
    exit;
}

$commentaire = 'Annulée par l\'équipe. Contact : ' . $modeContact . '. Motif : ' . $motif;

try {
    $pdo->beginTransaction();

    $pdo->prepare('
        UPDATE commande SET statut = "annulée", motif_annulation = ? WHERE commande_id = ?
    ')->execute([$commentaire, $commandeId]);

    $pdo->prepare('
        INSERT INTO suivi_commande (commande_id, statut, commentaire)
        VALUES (?, "annulée", ?)
    ')->execute([$commandeId, $commentaire]);

    // Remet le stock
    $pdo->prepare('
        UPDATE menu SET stock_disponible = stock_disponible + 1 WHERE menu_id = ?
    ')->execute([$commande['menu_id']]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/espace-employe.php?error=erreur_serveur#commandes');
    exit;
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=commande_annulee#commandes');
exit;