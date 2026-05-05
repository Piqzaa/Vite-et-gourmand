<?php
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

sessionStart();

if (!isConnected() || !in_array(getUserRole(), ['employe', 'admin'])) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo    = getDB();
$menuId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$menuId) {
    header('Location: ' . BASE_URL . '/espace-employe.php');
    exit;
}

$menu = $pdo->prepare('SELECT * FROM menu WHERE menu_id = ?');
$menu->execute([$menuId]);
$menu = $menu->fetch();

if (!$menu) {
    header('Location: ' . BASE_URL . '/espace-employe.php?error=menu_introuvable');
    exit;
}

$themes  = $pdo->query('SELECT * FROM theme')->fetchAll();
$regimes = $pdo->query('SELECT * FROM regime')->fetchAll();
$plats   = $pdo->query('SELECT plat_id, libelle, type FROM plat ORDER BY type, libelle')->fetchAll();

// Plats déjà associés à ce menu
$stmtAssoc = $pdo->prepare('SELECT plat_id FROM compose_menu WHERE menu_id = ?');
$stmtAssoc->execute([$menuId]);
$platsAssocies = array_column($stmtAssoc->fetchAll(), 'plat_id');

$platsByType = ['entrée' => [], 'plat' => [], 'dessert' => []];
foreach ($plats as $plat) {
    $platsByType[$plat['type']][] = $plat;
}

$title = 'Modifier le menu';
$description = 'Formulaire pour modifier un menu existant en tant qu\'employé de Vite & Gourmand.';
ob_start();
?>
    <section class="page-header">
        <div class="page-header__container">
            <span class="section-eyebrow">Espace employé</span>
            <h1 class="page-header__title">Modifier le menu</h1>
        </div>
    </section>

    <section class="commande-page">
        <div class="commande-page__container">
            <div class="commande-form-wrapper">
                <form action="assets/php/menu/update.php" method="POST" class="auth-form">
                    <input type="hidden" name="menu_id" value="<?= $menuId ?>">

                    <h2 class="commande-step__title">Informations générales</h2>
                    <div class="space-sm"></div>

                    <div class="form-group">
                        <label class="form-label" for="titre">Titre</label>
                        <input type="text" id="titre" name="titre" class="form-input"
                               value="<?= htmlspecialchars($menu['titre']) ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-input" rows="4" required>
                            <?= htmlspecialchars($menu['description']) ?>
                        </textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="prix">Prix de base (€)</label>
                            <input type="number" id="prix" name="prix_base" class="form-input"
                                   value="<?= $menu['prix_base'] ?>" min="0" step="0.01" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="personnes">Personnes minimum</label>
                            <input type="number" id="personnes" name="nombre_personne_min" class="form-input"
                                   value="<?= $menu['nombre_personne_min'] ?>" min="1" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="stock">Stock disponible</label>
                            <input type="number" id="stock" name="stock_disponible" class="form-input"
                                   value="<?= $menu['stock_disponible'] ?>" min="0" required />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="theme">Thème</label>
                            <select id="theme" name="theme_id" class="filters__select" required>
                                <?php foreach ($themes as $t): ?>
                                <option value="<?= $t['theme_id'] ?>"
                                    <?= $menu['theme_id'] == $t['theme_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['libelle']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="regime">Régime</label>
                            <select id="regime" name="regime_id" class="filters__select" required>
                                <?php foreach ($regimes as $r): ?>
                                <option value="<?= $r['regime_id'] ?>"
                                    <?= $menu['regime_id'] == $r['regime_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['libelle']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="conditions">Conditions particulières</label>
                        <textarea id="conditions" name="conditions_particulieres" class="form-input" rows="3">
                            <?= htmlspecialchars($menu['conditions_particulieres'] ?? '') ?>
                        </textarea>
                    </div>

                    <div class="space-md"></div>
                    <h2 class="commande-step__title">Composition du menu</h2>
                    <div class="space-sm"></div>

                    <?php
                    $labels = ['entrée' => 'Entrées', 'plat' => 'Plats', 'dessert' => 'Desserts'];
                    foreach ($platsByType as $type => $items):
                        if (empty($items)) continue;
                    ?>
                    <div class="form-group">
                        <label class="form-label"><?= $labels[$type] ?></label>
                        <div class="plats-checkboxes">
                            <?php foreach ($items as $plat): ?>
                            <label class="plat-checkbox">
                                <input type="checkbox" name="plats[]" value="<?= $plat['plat_id'] ?>"
                                    <?= in_array($plat['plat_id'], $platsAssocies) ? 'checked' : '' ?> />
                                <?= htmlspecialchars($plat['libelle']) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="space-md"></div>
                    <div class="commande-nav">
                        <a href="espace-employe.php#menus" class="btn btn--secondary">← Annuler</a>
                        <button type="submit" class="btn btn--primary">Enregistrer</button>
                    </div>

                </form>
            </div>
        </div>
    </section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>