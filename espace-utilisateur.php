<!doctype html>
<html lang="fr">
<?php 
$title = 'Détails du menu ';
require __DIR__ . '/includes/head.php'; ?>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content">
      <div class="dashboard">
        <!-- SIDEBAR -->
        <aside class="dashboard__sidebar">
          <div class="dashboard__user">
            <div class="dashboard__avatar">JD</div>
            <div>
              <p class="dashboard__username">Julie Dupont</p>
              <p class="dashboard__role">Utilisateur</p>
            </div>
          </div>

          <nav
            class="dashboard__nav"
            aria-label="Navigation espace utilisateur"
          >
            <ul class="dashboard__nav-list">
              <li>
                <a
                  href="#commandes"
                  class="dashboard__nav-link dashboard__nav-link--active"
                >
                  Mes commandes
                </a>
              </li>
              <li>
                <a href="#profil" class="dashboard__nav-link"> Mon profil </a>
              </li>
            </ul>
          </nav>
        </aside>

        <!-- CONTENU PRINCIPAL -->
        <div class="dashboard__content">
          <!-- MES COMMANDES -->
          <section class="dashboard__section" id="commandes">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Mes commandes</h1>
            </div>

            <!-- Commande en cours -->
            <div class="commande-card">
              <div class="commande-card__header">
                <div>
                  <span class="commande-card__id">#CMD-2025-001</span>
                  <h2 class="commande-card__menu">Menu des Fêtes</h2>
                </div>
                <span
                  class="commande-card__status commande-card__status--en-preparation"
                  >En préparation</span
                >
              </div>

              <div class="commande-card__infos">
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Date</span>
                  <span>25 décembre 2025 à 12h00</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Adresse</span>
                  <span>12 rue des Lilas, Bordeaux</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Personnes</span>
                  <span>10 personnes</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Total</span>
                  <span class="commande-card__price">405€</span>
                </div>
              </div>

              <!-- Suivi commande (visible quand statut = accepté) -->
              <div class="commande-suivi">
                <h3 class="commande-suivi__title">Suivi de la commande</h3>
                <ol class="commande-suivi__timeline">
                  <li class="commande-suivi__step commande-suivi__step--done">
                    <span class="commande-suivi__dot"></span>
                    <div>
                      <p class="commande-suivi__label">Commande reçue</p>
                      <p class="commande-suivi__date">20/12/2025 à 14h32</p>
                    </div>
                  </li>
                  <li class="commande-suivi__step commande-suivi__step--done">
                    <span class="commande-suivi__dot"></span>
                    <div>
                      <p class="commande-suivi__label">Acceptée</p>
                      <p class="commande-suivi__date">20/12/2025 à 16h00</p>
                    </div>
                  </li>
                  <li class="commande-suivi__step commande-suivi__step--active">
                    <span class="commande-suivi__dot"></span>
                    <div>
                      <p class="commande-suivi__label">En préparation</p>
                      <p class="commande-suivi__date">24/12/2025 à 08h00</p>
                    </div>
                  </li>
                  <li class="commande-suivi__step">
                    <span class="commande-suivi__dot"></span>
                    <div>
                      <p class="commande-suivi__label">En cours de livraison</p>
                    </div>
                  </li>
                  <li class="commande-suivi__step">
                    <span class="commande-suivi__dot"></span>
                    <div>
                      <p class="commande-suivi__label">Terminée</p>
                    </div>
                  </li>
                </ol>
              </div>
            </div>

            <!-- Commande en attente (modifiable/annulable) -->
            <div class="commande-card">
              <div class="commande-card__header">
                <div>
                  <span class="commande-card__id">#CMD-2025-002</span>
                  <h2 class="commande-card__menu">Menu Tradition</h2>
                </div>
                <span
                  class="commande-card__status commande-card__status--en-attente"
                  >En attente</span
                >
              </div>

              <div class="commande-card__infos">
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Date</span>
                  <span>31 décembre 2025 à 20h00</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Adresse</span>
                  <span>5 avenue des Chartrons, Bordeaux</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Personnes</span>
                  <span>6 personnes</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Total</span>
                  <span class="commande-card__price">210€</span>
                </div>
              </div>

              <!-- Actions disponibles tant que pas acceptée -->
              <div class="commande-card__actions">
                <a
                  href="commande.html?edit=2"
                  class="btn btn--secondary btn--sm"
                  >Modifier</a
                >
                <button
                  type="button"
                  class="btn btn--primary btn--sm"
                  data-id="2"
                >
                  Annuler
                </button>
              </div>
            </div>

            <!-- Commande terminée avec avis -->
            <div class="commande-card commande-card--done">
              <div class="commande-card__header">
                <div>
                  <span class="commande-card__id">#CMD-2025-003</span>
                  <h2 class="commande-card__menu">Menu Printanier</h2>
                </div>
                <span
                  class="commande-card__status commande-card__status--terminee"
                  >Terminée</span
                >
              </div>

              <div class="commande-card__infos">
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Date</span>
                  <span>15 avril 2025 à 13h00</span>
                </div>
                <div class="commande-card__info">
                  <span class="commande-card__info-label">Total</span>
                  <span class="commande-card__price">240€</span>
                </div>
              </div>

              <!-- Formulaire avis (visible quand terminée et pas encore noté) -->
              <div class="avis-form">
                <h3 class="avis-form__title">Donner votre avis</h3>
                <form action="php/avis/create.php" method="POST">
                  <input type="hidden" name="commande_id" value="3" />
                  <div class="form-group">
                    <label class="form-label">Note</label>
                    <div class="avis-form__stars">
                      <input
                        type="radio"
                        name="note"
                        id="star5"
                        value="5"
                      /><label for="star5">★</label>
                      <input
                        type="radio"
                        name="note"
                        id="star4"
                        value="4"
                      /><label for="star4">★</label>
                      <input
                        type="radio"
                        name="note"
                        id="star3"
                        value="3"
                      /><label for="star3">★</label>
                      <input
                        type="radio"
                        name="note"
                        id="star2"
                        value="2"
                      /><label for="star2">★</label>
                      <input
                        type="radio"
                        name="note"
                        id="star1"
                        value="1"
                      /><label for="star1">★</label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="commentaire"
                      >Commentaire</label
                    >
                    <textarea
                      id="commentaire"
                      name="commentaire"
                      class="form-input form-textarea"
                      rows="3"
                      placeholder="Partagez votre expérience..."
                    ></textarea>
                  </div>
                  <div class="space-sm"></div>
                  <button type="submit" class="btn btn--primary btn--sm">
                    Envoyer mon avis
                  </button>
                </form>
              </div>
            </div>
          </section>

          <!-- MON PROFIL -->
          <section class="dashboard__section" id="profil">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Mon profil</h1>
            </div>

            <form class="auth-form" action="php/user/update.php" method="POST">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="profil-prenom">Prénom</label>
                  <input
                    type="text"
                    id="profil-prenom"
                    name="prenom"
                    class="form-input"
                    value="Julie"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label" for="profil-nom">Nom</label>
                  <input
                    type="text"
                    id="profil-nom"
                    name="nom"
                    class="form-input"
                    value="Dupont"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-email">Email</label>
                <input
                  type="email"
                  id="profil-email"
                  name="email"
                  class="form-input"
                  value="julie.dupont@email.com"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-gsm">Téléphone</label>
                <input
                  type="tel"
                  id="profil-gsm"
                  name="gsm"
                  class="form-input"
                  value="06 12 34 56 78"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-adresse"
                  >Adresse postale</label
                >
                <input
                  type="text"
                  id="profil-adresse"
                  name="adresse"
                  class="form-input"
                  value="12 rue des Lilas, Bordeaux"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-password"
                  >Nouveau mot de passe</label
                >
                <input
                  type="password"
                  id="profil-password"
                  name="password"
                  class="form-input"
                  placeholder="Laisser vide pour ne pas modifier"
                />
              </div>

              <button type="submit" class="btn btn--primary">
                Enregistrer les modifications
              </button>
            </form>
          </section>
        </div>
      </div>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>