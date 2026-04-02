<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';

sessionStart();

if (!isConnected()) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php');
    exit;
}

$pdo        = getDB();
$userId     = getUserId();
$commandeId = (int)($_POST['commande_id'] ?? 0);

if (!$commandeId) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=commande_invalide');
    exit;
}

// Vérifie que la commande appartient bien à cet utilisateur et qu'elle est encore en attente
$stmt = $pdo->prepare('
    SELECT commande_id, statut 
    FROM commande 
    WHERE commande_id = ? AND utilisateur_id = ?
');
$stmt->execute([$commandeId, $userId]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=commande_introuvable');
    exit;
}

if ($commande['statut'] !== 'en attente') {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=annulation_impossible');
    exit;
}

try {
    $pdo->beginTransaction();

    // Met à jour le statut
    $pdo->prepare('
        UPDATE commande SET statut = "annulée" WHERE commande_id = ?
    ')->execute([$commandeId]);

    // Insère dans le suivi
    $pdo->prepare('
        INSERT INTO suivi_commande (commande_id, statut, commentaire)
        VALUES (?, "annulée", "Commande annulée par le client")
    ')->execute([$commandeId]);

    // Remet le stock à jour
    $pdo->prepare('
        UPDATE menu m
        JOIN commande c ON c.menu_id = m.menu_id
        SET m.stock_disponible = m.stock_disponible + 1
        WHERE c.commande_id = ?
    ')->execute([$commandeId]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=erreur_serveur');
    exit;
}

header('Location: ' . BASE_URL . '/espace-utilisateur.php?success=commande_annulee#commandes');
exit;