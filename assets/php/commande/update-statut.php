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

$pdo        = getDB();
$commandeId = (int)($_POST['commande_id'] ?? 0);
$statut     = trim($_POST['statut'] ?? '');

$statutsAutorisés = [
    'en attente', 'accepté', 'en préparation',
    'en cours de livraison', 'livré',
    'en attente du retour de matériel', 'terminée'
];

if (!$commandeId || !in_array($statut, $statutsAutorisés)) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=donnees_invalides');
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->prepare('
        UPDATE commande SET statut = ? WHERE commande_id = ?
    ')->execute([$statut, $commandeId]);

    $pdo->prepare('
        INSERT INTO suivi_commande (commande_id, statut, commentaire)
        VALUES (?, ?, ?)
    ')->execute([$commandeId, $statut, 'Statut mis à jour par l\'équipe']);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/espace-employe.php?error=erreur_serveur');
    exit;
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=statut_mis_a_jour#commandes');
exit;