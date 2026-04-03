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
$note       = (int)($_POST['note'] ?? 0);
$commentaire = trim($_POST['commentaire'] ?? '');

// Validation
if (!$commandeId || $note < 1 || $note > 5) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=avis_invalide#commandes');
    exit;
}

// Vérifie que la commande appartient à l'utilisateur et est bien terminée
$stmt = $pdo->prepare('
    SELECT commande_id FROM commande 
    WHERE commande_id = ? AND utilisateur_id = ? AND statut = "terminée"
');
$stmt->execute([$commandeId, $userId]);
if (!$stmt->fetch()) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=avis_non_autorise#commandes');
    exit;
}

// Vérifie qu'un avis n'existe pas déjà pour cette commande
$stmt = $pdo->prepare('SELECT avis_id FROM avis WHERE commande_id = ?');
$stmt->execute([$commandeId]);
if ($stmt->fetch()) {
    header('Location: ' . BASE_URL . '/espace-utilisateur.php?error=avis_existant#commandes');
    exit;
}

// Insertion — statut "en attente" par défaut, validé par employé/admin
$stmt = $pdo->prepare('
    INSERT INTO avis (note, commentaire, est_valide, date_publication, utilisateur_id, commande_id)
    VALUES (?, ?, 0, NOW(), ?, ?)
');
$stmt->execute([$note, $commentaire, $userId, $commandeId]);

header('Location: ' . BASE_URL . '/espace-utilisateur.php?success=avis_envoye#commandes');
exit;