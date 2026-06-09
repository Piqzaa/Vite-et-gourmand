<?php
ob_start();
?>
  <div class="menu-detail__nav">
    <a href="index.php?page=menus" class="menu-detail__back">← Retour aux menus</a>
  </div>

  <section class="menu-detail">
    <div class="menu-detail__container">
      <div class="menu-detail__left">
    <!-- Galerie -->
    <div class="menu-gallery">
    <div class="menu-gallery__main">
        <?php 
        // Première image disponible comme image principale
        $firstImg = !empty($plats) && $plats[0]['image_path'] 
            ? 'assets/img/plats/' . htmlspecialchars($plats[0]['image_path'])
            : 'assets/img/menu-placeholder.jpg';
        ?>
        <img
            src="<?= $firstImg ?>"
            alt="<?= htmlspecialchars($menu->getTitre()) ?>"
            class="menu-gallery__img"
            id="gallery-main-img"
            onerror="this.onerror=null; this.src='assets/img/menu-placeholder.jpg';"
        />
    </div>
    <div class="menu-gallery__thumbs">
        <?php foreach ($plats as $i => $plat): 
            if (!$plat['image_path']) continue;
            $imgPath = 'assets/img/plats/' . htmlspecialchars($plat['image_path']);
        ?>
        <img
            src="<?= $imgPath ?>"
            alt="<?= htmlspecialchars($plat['plat_titre']) ?>"
            class="menu-gallery__thumb <?= $i === 0 ? 'menu-gallery__thumb--active' : '' ?>"
            onerror="this.onerror=null; this.src='assets/img/menu-placeholder.jpg';"
        />
        <?php endforeach; ?>
    </div>
</div>

    <!-- Plats -->
    <div class="menu-plats">
        <h2 class="menu-plats__title">Composition du menu</h2>

        <?php
        $labels = ['entrée' => 'Entrées', 'plat' => 'Plats', 'dessert' => 'Desserts'];
        foreach ($platsByType as $type => $items):
            if (empty($items)) continue;
        ?>
        <div class="menu-plats__group">
            <h3 class="menu-plats__category"><?= $labels[$type] ?></h3>
            <ul class="menu-plats__list">
                <?php foreach ($items as $item): ?>
                <li class="menu-plat">
                    <span class="menu-plat__name"><?= htmlspecialchars($item['plat_titre']) ?></span>
                    <?php if ($item['allergenes']): ?>
                    <div class="menu-plat__allergenes">
                        <?php foreach (explode(', ', $item['allergenes']) as $allergene): ?>
                        <span class="allergene"><?= htmlspecialchars($allergene) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </div>
</div>
      <div class="menu-detail__right">
        <div class="menu-detail__header">
          <div class="menu-detail__tags">
            <span class="menu-card__tag"><?= htmlspecialchars($menu->getThemeLabel() ?? '—') ?></span>
            <span class="menu-card__tag"><?= htmlspecialchars($menu->getRegimeLabel() ?? '—') ?></span>
          </div>
          <h1 class="menu-detail__title"><?= htmlspecialchars($menu->getTitre()) ?></h1>
          <p class="menu-detail__desc"><?= htmlspecialchars($menu->getDescription()) ?></p>
        </div>

        <div class="menu-detail__infos">
          <div class="menu-info">
            <span class="menu-info__label">Prix de base</span>
            <span class="menu-info__value menu-info__value--price"><?= $menu->getPrixBase() ?>€</span>
          </div>
          <div class="menu-info">
            <span class="menu-info__label">Personnes minimum</span>
            <span class="menu-info__value"><?= $menu->getNombrePersonneMin() ?> personnes</span>
          </div>
          <div class="menu-info">
            <span class="menu-info__label">Régime</span>
            <span class="menu-info__value"><?= htmlspecialchars($menu->getRegimeLabel() ?? '—') ?></span>
          </div>
          <div class="menu-info">
            <span class="menu-info__label">Stock disponible</span>
            <span class="menu-info__value"><?= $menu->getStockDisponible() ?> commandes restantes</span>
          </div>
        </div>

        <?php if ($menu->getConditionsParticulieres()): ?>
        <div class="menu-conditions">
          <div class="menu-conditions__header">
            <span class="menu-conditions__icon" aria-hidden="true">⚠️</span>
            <h2 class="menu-conditions__title">Conditions importantes</h2>
          </div>
          <p><?= htmlspecialchars($menu->getConditionsParticulieres()) ?></p>
        </div>
        <?php endif; ?>

        <div class="menu-detail__cta">
          <a href="index.php?page=commande&menu=<?= $menu->getId() ?>" class="btn btn--primary btn--full">
            Commander ce menu
          </a>
        </div>
      </div>

    </div>
  </section>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout/main.php';
?>
