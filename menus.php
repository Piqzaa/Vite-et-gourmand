<?php include __DIR__ . '/includes/header.php'; ?>
<body>
    <main id="main-content">
      <!-- PAGE HEADER -->
      <section class="page-header">
        <div class="page-header__container">
          <span class="section-eyebrow">Traiteur bordelais</span>
          <h1 class="page-header__title">Nos menus</h1>
          <p class="page-header__sub">
            Découvrez nos menus faits maison — filtrez selon vos envies et vos
            contraintes.
          </p>
        </div>
      </section>

      <!-- MENUS + FILTRES -->
      <section class="menus-page">
        <div class="menus-page__container">
          <aside class="filters" aria-label="Filtres de recherche">
            <h2 class="filters__title" id="filters-title">Filtrer les menus</h2>

            <div class="filters__group">
              <label class="filters__label" for="filter-prix-max"
                >Prix maximum</label
              >
              <div class="filters__range-wrapper">
                <input
                  type="range"
                  id="filter-prix-max"
                  class="filters__range"
                  min="0"
                  max="200"
                  value="200"
                />
                <div class="filters__range-value">
                  <span>0€</span>
                  <span id="prix-max-value">200€</span>
                </div>
              </div>
            </div>

            <div class="filters__group">
              <label class="filters__label">Fourchette de prix</label>
              <div class="filters__row">
                <div class="filters__input-wrapper">
                  <label class="filters__sublabel" for="filter-prix-min"
                    >Min (€)</label
                  >
                  <input
                    type="number"
                    id="filter-prix-min"
                    class="filters__input"
                    min="0"
                    max="200"
                    value="0"
                    placeholder="0"
                  />
                </div>
                <div class="filters__input-wrapper">
                  <label class="filters__sublabel" for="filter-prix-max2"
                    >Max (€)</label
                  >
                  <input
                    type="number"
                    id="filter-prix-max2"
                    class="filters__input"
                    min="0"
                    max="200"
                    value="200"
                    placeholder="200"
                  />
                </div>
              </div>
            </div>

            <div class="filters__group">
              <label class="filters__label" for="filter-theme">Thème</label>
              <select id="filter-theme" class="filters__select">
                <option value="">Tous les thèmes</option>
                <option value="noel">Noël</option>
                <option value="paques">Pâques</option>
                <option value="classique">Classique</option>
                <option value="evenement">Événement</option>
              </select>
            </div>

            <div class="filters__group">
              <label class="filters__label" for="filter-regime">Régime</label>
              <select id="filter-regime" class="filters__select">
                <option value="">Tous les régimes</option>
                <option value="classique">Classique</option>
                <option value="vegetarien">Végétarien</option>
                <option value="vegan">Vegan</option>
                <option value="sans-gluten">Sans gluten</option>
              </select>
            </div>

            <div class="filters__group">
              <label class="filters__label" for="filter-personnes"
                >Nombre de personnes min.</label
              >
              <input
                type="number"
                id="filter-personnes"
                class="filters__input"
                min="1"
                value=""
                placeholder="Ex: 6"
              />
            </div>

            <button
              class="filters__reset btn btn--secondary btn--full"
              id="filters-reset"
              type="button"
            >
              Réinitialiser les filtres
            </button>
          </aside>

          <!-- LISTE DES MENUS -->
          <div class="menus-list">
            <div class="menus-list__header">
              <p class="menus-list__count">
                <span id="menus-count">6</span> menus disponibles
              </p>
            </div>

            <div class="menus-list__grid" id="menus-grid">
              <?php
                require_once __DIR__ . '/assets/php/config/db.php';

                $pdo = getDB();
                $stmt = $pdo->query('
                    SELECT m.menu_id, m.titre, m.description, m.nombre_personne_min,
                          m.prix_base, m.stock_disponible,
                          t.libelle AS theme,
                          r.libelle AS regime
                    FROM menu m
                    LEFT JOIN theme t ON m.theme_id = t.theme_id
                    LEFT JOIN regime r ON m.regime_id = r.regime_id
                ');
                $menus = $stmt->fetchAll(); ?>

              <?php foreach ($menus as $menu): ?>
              <article class="menu-card">
                  <div class="menu-card__img-wrapper">
                      <img
                          src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600"
                          alt="<?= htmlspecialchars($menu['titre']) ?>"
                          class="menu-card__img"
                      />
                  </div>
                  <div class="menu-card__body">
                      <span class="menu-card__tag"><?= htmlspecialchars($menu['theme'] ?? '—') ?></span>
                      <h3 class="menu-card__title"><?= htmlspecialchars($menu['titre']) ?></h3>
                      <p class="menu-card__desc"><?= htmlspecialchars($menu['description']) ?></p>
                      <div class="menu-card__meta">
                          <span class="menu-card__price">À partir de <?= $menu['prix_base'] ?>€</span>
                          <span class="menu-card__persons"><?= $menu['nombre_personne_min'] ?> pers. min.</span>
                      </div>
                      <a href="menu-detail.php?id=<?= $menu['menu_id'] ?>" class="btn btn--outline btn--full">
                          Voir le détail
                      </a>
                  </div>
              </article>
              <?php endforeach; ?>
              <!-- Message si aucun résultat (caché par défaut) -->
              <div
                class="menus-list__empty"
                id="menus-empty"
                aria-live="polite"
                hidden
              >
                <p>Aucun menu ne correspond à vos critères.</p>
                <button
                  class="btn btn--primary"
                  id="reset-from-empty"
                  type="button"
                >
                  Réinitialiser les filtres
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
<?php include __DIR__ . '/includes/footer.php'; ?>


