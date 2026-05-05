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

// UPLOAD IMAGE
$uploadDir = __DIR__ . '/../../../assets/img/plats/';
$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['image']['tmp_name'];
    $originalName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileType = $_FILES['image']['type'];

    // Validation type MIME
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!in_array($fileType, $allowedTypes)) {
        header('Location: ' . BASE_URL . '/plat-create.php?error=format_invalide');
        exit;
    }

    // Validation taille (2 Mo max)
    if ($fileSize > 2 * 1024 * 1024) {
        header('Location: ' . BASE_URL . '/plat-create.php?error=fichier_trop_lourd');
        exit;
    }

    // Générer un nom unique
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $newFileName = uniqid('plat_', true) . '.' . $extension;
    $destination = $uploadDir . $newFileName;

    // Créer le dossier si n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Déplacer le fichier
    if (!move_uploaded_file($tmpName, $destination)) {
        header('Location: ' . BASE_URL . '/plat-create.php?error=upload_echoue');
        exit;
    }

    $imagePath = $newFileName;
} else {
    // Image obligatoire
    header('Location: ' . BASE_URL . '/plat-create.php?error=image_manquante');
    exit;
}

try {
    $pdo->beginTransaction();

    // Insertion avec image_path
    $pdo->prepare('INSERT INTO plat (libelle, type, image_path) VALUES (?, ?, ?)')->execute([$libelle, $type, $imagePath]);
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
    
    // Supprimer l'image uploadée si échec en BDD
    if ($imagePath && file_exists($uploadDir . $imagePath)) {
        unlink($uploadDir . $imagePath);
    }
    
    header('Location: ' . BASE_URL . '/plat-create.php?error=erreur_serveur');
    exit;
}

header('Location: ' . BASE_URL . '/espace-employe.php?success=plat_cree#menus');
exit;