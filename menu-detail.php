<?php
require_once __DIR__ . '/assets/php/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: menus.php');
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare('
    SELECT m.menu_id, m.titre, m.description, m.nombre_personne_min,
          m.prix_base, m.stock_disponible, m.conditions_particulieres,
          t.libelle AS theme,
          r.libelle AS regime
    FROM menu m
    LEFT JOIN theme t ON m.theme_id = t.theme_id
    LEFT JOIN regime r ON m.regime_id = r.regime_id
    WHERE m.menu_id = :id
');
$stmt->execute([':id' => $id]);
$menu = $stmt->fetch();

if (!$menu) {
    header('Location: menus.php');
    exit;
}

$stmtPlats = $pdo->prepare('
    SELECT p.plat_id, p.libelle AS plat_titre, p.type,
          GROUP_CONCAT(a.libelle SEPARATOR ", ") AS allergenes
    FROM compose_menu cm
    JOIN plat p ON cm.plat_id = p.plat_id
    LEFT JOIN plat_allergene pa ON p.plat_id = pa.plat_id
    LEFT JOIN allergene a ON pa.allergene_id = a.allergene_id
    WHERE cm.menu_id = :id
    GROUP BY p.plat_id, p.libelle, p.type
');
$stmtPlats->execute([':id' => $id]);
$plats = $stmtPlats->fetchAll();

$platsByType = ['entrée' => [], 'plat' => [], 'dessert' => []];
foreach ($plats as $plat) {
    $type = $plat['type'];
    if (isset($platsByType[$type])) {
        $platsByType[$type][] = $plat;
    }
}
?>
?>
<!doctype html>
<html lang="fr">
<?php 
$title = 'Détails du menu ';
require __DIR__ . '/includes/head.php'; ?>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>


<main id="main-content">
  <div class="menu-detail__nav">
    <a href="menus.php" class="menu-detail__back">← Retour aux menus</a>
  </div>

  <section class="menu-detail">
    <div class="menu-detail__container">
      <div class="menu-detail__left">
    <!-- Galerie statique pour l'instant -->
    <div class="menu-gallery">
        <div class="menu-gallery__main">
            <img
                src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800"
                alt="<?= htmlspecialchars($menu['titre']) ?>"
                class="menu-gallery__img"
                id="gallery-main-img"
            />
        </div>
    </div>

    <!-- Plats depuis la BDD -->
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
          <a href="commande.php?menu=<?= $menu['menu_id'] ?>" class="btn btn--primary btn--full">
            Commander ce menu
          </a>
        </div>
      </div>

    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>