<?php
ob_start();
?>
    <section class="page-header">
        <div class="page-header__container">
            <span class="section-eyebrow">Administration</span>
            <h1 class="page-header__title">Modifier le menu</h1>
        </div>
    </section>

    <section class="commande-page">
        <div class="commande-page__container">
            <div class="commande-form-wrapper">
                <form action="index.php?page=menu-edit&action=process" method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="menu_id" value="<?= $menu['menu_id'] ?>">

                    <h2 class="commande-step__title">Informations générales</h2>
                    <div class="space-sm"></div>

                    <div class="form-group">
                        <label class="form-label" for="titre">Titre</label>
                        <input type="text" id="titre" name="titre" class="form-input"
                               value="<?= htmlspecialchars($menu['titre']) ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-input" rows="4" required><?= htmlspecialchars($menu['description']) ?></textarea>
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
                        <textarea id="conditions" name="conditions_particulieres" class="form-input" rows="3"><?= htmlspecialchars($menu['conditions_particulieres'] ?? '') ?></textarea>
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
                        <a href="index.php?page=espace-admin#menus" class="btn btn--secondary">← Annuler</a>
                        <button type="submit" class="btn btn--primary">Enregistrer</button>
                    </div>

                </form>
            </div>
        </div>
    </section>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layout.php';
?>
