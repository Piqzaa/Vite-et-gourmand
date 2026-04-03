<?php
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

sessionStart();


if (!isConnected()) {
    header('Location: connexion.php?redirect=commande.php' . (isset($_GET['menu']) ? '&menu=' . (int)$_GET['menu'] : ''));
    exit;
}

$pdo = getDB();

$user = [
    'prenom'         => $_SESSION['user_prenom'] ?? '',
    'nom'            => $_SESSION['user_nom'] ?? '',
    'email'          => $_SESSION['user_email'] ?? '',
    'gsm'            => $_SESSION['user_gsm'] ?? '',
    'adresse_postale'=> $_SESSION['user_adresse'] ?? '',
    'ville'          => $_SESSION['user_ville'] ?? '' 
];


// Pré-sélection menu depuis GET
$menuPreselect = isset($_GET['menu']) ? (int)$_GET['menu'] : 0;

// Récupère tous les menus pour le select
$stmtMenus = $pdo->query('
    SELECT menu_id, titre, prix_base, nombre_personne_min, conditions_particulieres
    FROM menu
    WHERE stock_disponible > 0
    ORDER BY titre
');
$menus = $stmtMenus->fetchAll();
$title = 'Commander un menu';
$description = 'Remplissez le formulaire pour commander votre menu traiteur sur Vite & Gourmand. Livraison rapide à Bordeaux et ses environs. Choisissez votre menu, indiquez vos informations de livraison, et finalisez votre commande en quelques clics.';
ob_start();
?>
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
              action="assets/php/commande/create.php"
              method="POST"
              novalidate
              id="commande-form"
            >
              <!-- ÉTAPE 1 : INFOS CLIENT -->
              <div class="commande-step" id="step-1">
                <h2 class="commande-step__title">Informations client</h2>
                <div class="space-sm"></div>
                <div class="form-row">
                  
                  <div class="form-group">
                    
                    <label class="form-label" for="prenom">Prénom</label>
                    <input
                      type="text"
                      id="prenom"
                      name="prenom"
                      class="form-input"
                      value="<?= htmlspecialchars($user['prenom']) ?>"
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
                      value="<?= htmlspecialchars($user['nom']) ?>"
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
                    value="<?= htmlspecialchars($user['email']) ?>"
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
                    value="<?= htmlspecialchars($user['gsm']) ?>"
                    required
                    autocomplete="tel"
                  />
                </div>
                <div class="space-md"></div>
                <h2 class="commande-step__title">Informations de livraison</h2>
                
                <div class="form-group">
                  <div class="space-sm"></div>
                  <label class="form-label" for="adresse-livraison"
                    >Adresse de livraison</label
                  >
                  <input
                    type="text"
                    id="adresse-livraison"
                    name="adresse_livraison"
                    class="form-input"
                    value="<?= htmlspecialchars($user['adresse_postale']) ?>"
                    required
                    autocomplete="street-address"
                  />
                  <div class="form-group">
                  <label class="form-label" for="ville-livraison"
                    >Ville</label
                  >
                  <input
                    type="text"
                    id="ville-livraison"
                    name="ville_livraison"
                    class="form-input"
                    value="<?= htmlspecialchars($user['ville']) ?>"
                    required
                    autocomplete="city"
                  />
                  </div>
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
                <div class="space-md"></div>
              </div>

              <!-- ÉTAPE 2 : MENU -->
              <div class="commande-step" id="step-2">
                <h2 class="commande-step__title">Choix du menu</h2>

                <div class="form-group">
                  <label class="form-label" for="menu-choisi"
                    >Menu sélectionné</label
                  >
                  <select id="menu-choisi" name="menu_id" class="filters__select" required>
                    <option value="">Choisir un menu</option>
                    <?php foreach ($menus as $m): ?>
                    <option value="<?= $m['menu_id'] ?>"
                        data-prix="<?= $m['prix_base'] ?>"
                        data-min="<?= $m['nombre_personne_min'] ?>"
                        data-conditions="<?= htmlspecialchars($m['conditions_particulieres'] ?? '') ?>"
                        <?= $menuPreselect === $m['menu_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['titre']) ?> — <?= $m['prix_base'] ?>€ / <?= $m['nombre_personne_min'] ?> pers. min.
                    </option>
                    <?php endforeach; ?>
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
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>