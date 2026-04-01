<!doctype html>
<html lang="fr">
<?php 
$title = 'Nos menus';
require __DIR__ . '/includes/head.php'; ?>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

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
                <option value="1">Noël</option>
                <option value="2">Pâques</option>
                <option value="3">Classique</option>
                <option value="4">Événement</option>
              </select>
            </div>

            <div class="filters__group">
              <label class="filters__label" for="filter-regime">Régime</label>
              <select id="filter-regime" class="filters__select">
                <option value="">Tous les régimes</option>
                <option value="1">Classique</option>
                <option value="2">Végétarien</option>
                <option value="3">Vegan</option>
                <option value="4">Sans gluten</option>
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

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


