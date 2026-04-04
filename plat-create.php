<?php
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

sessionStart();

if (!isConnected() || !in_array(getUserRole(), ['employe', 'admin'])) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo       = getDB();
$allergenes = $pdo->query('SELECT * FROM allergene ORDER BY libelle')->fetchAll();

$title = 'Créer un plat - Espace employé';
$description = 'Formulaire pour créer un nouveau plat en tant qu\'employé de Vite & Gourmand.';
ob_start();
?>
    <section class="page-header">
        <div class="page-header__container">
            <span class="section-eyebrow">Espace employé</span>
            <h1 class="page-header__title">Créer un plat</h1>
        </div>
    </section>

    <section class="commande-page">
        <div class="commande-page__container">
            <div class="commande-form-wrapper">
                <form action="assets/php/plat/create.php" method="POST" class="auth-form">

                    <div class="form-group">
                        <label class="form-label" for="libelle">Nom du plat</label>
                        <input type="text" id="libelle" name="libelle" class="form-input" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="type">Type</label>
                        <select id="type" name="type" class="filters__select" required>
                            <option value="">Choisir un type</option>
                            <option value="entrée">Entrée</option>
                            <option value="plat">Plat</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Allergènes</label>
                        <div class="plats-checkboxes">
                            <?php foreach ($allergenes as $a): ?>
                            <label class="plat-checkbox">
                                <input type="checkbox" name="allergenes[]" value="<?= $a['allergene_id'] ?>" />
                                <?= htmlspecialchars($a['libelle']) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="space-md"></div>
                    <div class="commande-nav">
                        <a href="espace-employe.php#menus" class="btn btn--secondary">← Annuler</a>
                        <button type="submit" class="btn btn--primary">Créer le plat</button>
                    </div>

                </form>
            </div>
        </div>
    </section>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/includes/layout.php';
?>