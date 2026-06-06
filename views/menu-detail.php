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
            alt="<?= htmlspecialchars($menu['titre']) ?>"
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
            <span class="menu-card__tag"><?= htmlspecialchars($menu['theme'] ?? '—') ?></span>
            <span class="menu-card__tag"><?= htmlspecialchars($menu['regime'] ?? '—') ?></span>
          </div>
          <h1 class="menu-detail__title"><?= htmlspecialchars($menu['titre']) ?></h1>
          <p class="menu-detail__desc"><?= htmlspecialchars($menu['description']) ?></p>
        </div>

        <div class="menu-detail__infos">
          <div class="menu-info">
            <span class="menu-info__label">Prix de base</span>
            <span class="menu-info__value menu-info__value--price"><?= $menu['prix_base'] ?>€</span>
          </div>
          <div class="menu-info">
            <span class="menu-info__label">Personnes minimum</span>
            <span class="menu-info__value"><?= $menu['nombre_personne_min'] ?> personnes</span>
          </div>
          <div class="menu-info">
            <span class="menu-info__label">Régime</span>
            <span class="menu-info__value"><?= htmlspecialchars($menu['regime'] ?? '—') ?></span>
          </div>
          <div class="menu-info">
            <span class="menu-info__label">Stock disponible</span>
            <span class="menu-info__value"><?= $menu['stock_disponible'] ?> commandes restantes</span>
          </div>
        </div>

        <?php if ($menu['conditions_particulieres']): ?>
        <div class="menu-conditions">
          <div class="menu-conditions__header">
            <span class="menu-conditions__icon" aria-hidden="true">⚠️</span>
            <h2 class="menu-conditions__title">Conditions importantes</h2>
          </div>
          <p><?= htmlspecialchars($menu['conditions_particulieres']) ?></p>
        </div>
        <?php endif; ?>

        <div class="menu-detail__cta">
          <a href="index.php?page=commande&menu=<?= $menu['menu_id'] ?>" class="btn btn--primary btn--full">
            Commander ce menu
          </a>
        </div>
      </div>

    </div>
  </section>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layout.php';
?>
