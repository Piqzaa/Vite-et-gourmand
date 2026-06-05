<?php 
require_once __DIR__ . '/../assets/php/includes/session.php';
sessionStart();
?>
<!doctype html>
<html lang="fr">
<?php require __DIR__ . '/head.php'; ?>
<body>
    <?php require __DIR__ . '/header.php'; ?>

    <main id="main-content">
        <?= $content ?>
    </main>

    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
