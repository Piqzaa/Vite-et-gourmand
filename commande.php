<!doctype html>
<html lang="fr">

<?php 
$title = 'Détails du menu ';
require __DIR__ . '/includes/head.php'; ?>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content">
      <section class="page-header">
        <div class="page-header__container">
          <span class="section-eyebrow">Espace commande</span>
          <h1 class="page-header__title">Commander un menu</h1>
          <p class="page-header__sub">
            Remplissez le formulaire ci-dessous pour finaliser votre commande.
          </p>
        </div>
      </section>

      <section class="commande-page">
        <div class="commande-page__container">
          <div class="commande-form-wrapper">
            <div class="stepper" aria-label="Étapes de la commande">
              <div class="stepper__item stepper__item--active">
                <span class="stepper__number">1</span>
                <span class="stepper__label">Mes informations</span>
              </div>
              <div class="stepper__divider"></div>
              <div class="stepper__item">
                <span class="stepper__number">2</span>
                <span class="stepper__label">Mon menu</span>
              </div>
              <div class="stepper__divider"></div>
              <div class="stepper__item">
                <span class="stepper__number">3</span>
                <span class="stepper__label">Récapitulatif</span>
              </div>
            </div>

            <form
              class="auth-form"
              action="php/commande/create.php"
              method="POST"
              novalidate
              id="commande-form"
            >
              <!-- ÉTAPE 1 : INFOS CLIENT -->
              <div class="commande-step" id="step-1">
                <h2 class="commande-step__title">Informations client</h2>

                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="prenom">Prénom</label>
                    <input
                      type="text"
                      id="prenom"
                      name="prenom"
                      class="form-input"
                      placeholder="Julie"
                      required
                      autocomplete="given-name"
                    />
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="nom">Nom</label>
                    <input
                      type="text"
                      id="nom"
                      name="nom"
                      class="form-input"
                      placeholder="Dupont"
                      required
                      autocomplete="family-name"
                    />
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label" for="email">Email</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="votre@email.com"
                    required
                    autocomplete="email"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label" for="gsm">Téléphone</label>
                  <input
                    type="tel"
                    id="gsm"
                    name="gsm"
                    class="form-input"
                    placeholder="06 12 34 56 78"
                    required
                    autocomplete="tel"
                  />
                </div>

                <h2 class="commande-step__title">Informations de livraison</h2>

                <div class="form-group">
                  <label class="form-label" for="adresse-livraison"
                    >Adresse de livraison</label
                  >
                  <input
                    type="text"
                    id="adresse-livraison"
                    name="adresse_livraison"
                    class="form-input"
                    placeholder="12 rue des Lilas, Bordeaux"
                    required
                    autocomplete="street-address"
                  />
                  <p class="form-hint">
                    Livraison gratuite à Bordeaux. Hors Bordeaux : 5€ +
                    0,59€/km.
                  </p>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="date-livraison"
                      >Date de la prestation</label
                    >
                    <input
                      type="date"
                      id="date-livraison"
                      name="date_livraison"
                      class="form-input"
                      required
                    />
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="heure-livraison"
                      >Heure souhaitée</label
                    >
                    <input
                      type="time"
                      id="heure-livraison"
                      name="heure_livraison"
                      class="form-input"
                      required
                    />
                  </div>
                </div>
              </div>

              <!-- ÉTAPE 2 : MENU -->
              <div class="commande-step" id="step-2">
                <h2 class="commande-step__title">Choix du menu</h2>

                <div class="form-group">
                  <label class="form-label" for="menu-choisi"
                    >Menu sélectionné</label
                  >
                  <select
                    id="menu-choisi"
                    name="menu_id"
                    class="filters__select"
                    required
                  >
                    <option value="">Choisir un menu</option>
                    <!-- Options générées dynamiquement via PHP -->
                    <option value="1">
                      Menu des Fêtes — 45€ / 8 pers. min.
                    </option>
                    <option value="2">
                      Menu Tradition — 35€ / 6 pers. min.
                    </option>
                    <option value="3">
                      Menu Printanier — 40€ / 6 pers. min.
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label" for="nb-personnes"
                    >Nombre de personnes</label
                  >
                  <input
                    type="number"
                    id="nb-personnes"
                    name="nb_personnes"
                    class="form-input"
                    min="1"
                    placeholder="Ex: 10"
                    required
                  />
                  <p class="form-hint" id="personnes-hint">
                    Minimum requis selon le menu choisi.
                  </p>
                </div>

                <!-- Conditions du menu sélectionné -->
                <div class="menu-conditions" id="commande-conditions">
                  <div class="menu-conditions__header">
                    <span class="menu-conditions__icon">⚠️</span>
                    <h3 class="menu-conditions__title">
                      Conditions de ce menu
                    </h3>
                  </div>
                  <ul class="menu-conditions__list">
                    <li>
                      Ce menu doit être commandé
                      <strong>minimum 72h avant</strong> la prestation.
                    </li>
                    <li>
                      Conservation au réfrigérateur, à consommer dans les
                      <strong>24h</strong> après livraison.
                    </li>
                  </ul>
                </div>
              </div>

              <!-- ÉTAPE 3 : RECAP -->
              <div class="commande-step" id="step-3">
                <h2 class="commande-step__title">
                  Récapitulatif de votre commande
                </h2>

                <div class="commande-recap">
                  <div class="commande-recap__row">
                    <span>Menu sélectionné</span>
                    <span id="recap-menu">—</span>
                  </div>
                  <div class="commande-recap__row">
                    <span>Nombre de personnes</span>
                    <span id="recap-personnes">—</span>
                  </div>
                  <div class="commande-recap__row">
                    <span>Date de livraison</span>
                    <span id="recap-date">—</span>
                  </div>
                  <div class="commande-recap__row">
                    <span>Adresse</span>
                    <span id="recap-adresse">—</span>
                  </div>
                  <div class="commande-recap__divider"></div>
                  <div class="commande-recap__row">
                    <span>Prix du menu</span>
                    <span id="recap-prix-menu">—</span>
                  </div>
                  <div class="commande-recap__row">
                    <span>Frais de livraison</span>
                    <span id="recap-livraison">Gratuit</span>
                  </div>
                  <div class="commande-recap__row" id="recap-reduction-row">
                    <span>Réduction 10%</span>
                    <span id="recap-reduction">—</span>
                  </div>
                  <div class="commande-recap__divider"></div>
                  <div class="commande-recap__row commande-recap__row--total">
                    <span>Total</span>
                    <span id="recap-total">—</span>
                  </div>
                </div>
              </div>

              <!-- NAVIGATION ENTRE ÉTAPES -->
              <div class="commande-nav">
                <button
                  type="button"
                  class="btn btn--secondary"
                  id="btn-prev"
                  style="display: none"
                >
                  ← Étape précédente
                </button>
                <button type="button" class="btn btn--primary" id="btn-next">
                  Étape suivante →
                </button>
                <button
                  type="submit"
                  class="btn btn--primary"
                  id="btn-submit"
                  style="display: none"
                >
                  Confirmer la commande
                </button>
              </div>
            </form>
          </div>

          <!-- RECAP PRIX (sticky) -->
          <aside class="commande-aside">
            <div class="commande-aside__card">
              <h2 class="commande-aside__title">Votre commande</h2>

              <div class="commande-aside__row">
                <span>Menu</span>
                <span id="aside-menu">—</span>
              </div>
              <div class="commande-aside__row">
                <span>Personnes</span>
                <span id="aside-personnes">—</span>
              </div>
              <div class="commande-aside__row">
                <span>Livraison</span>
                <span id="aside-livraison">—</span>
              </div>
              <div class="commande-aside__row">
                <span>Réduction</span>
                <span id="aside-reduction">—</span>
              </div>

              <div class="commande-aside__divider"></div>

              <div class="commande-aside__total">
                <span>Total</span>
                <span id="aside-total">—</span>
              </div>

              <p class="commande-aside__note">
                Un email de confirmation vous sera envoyé après validation.
              </p>
            </div>
          </aside>
        </div>
      </section>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>
