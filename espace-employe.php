<?php
$title = 'Espace employé';
$description = 'Bienvenue dans votre espace employé sur Vite & Gourmand. Gérez les commandes, consultez les avis clients, et mettez à jour les horaires d\'ouverture pour offrir la meilleure expérience à nos clients à Bordeaux.';
ob_start();
?>
      <div class="dashboard">
        <!-- SIDEBAR -->
        <aside class="dashboard__sidebar">
          <div class="dashboard__user">
            <div class="dashboard__avatar">JO</div>
            <div>
              <p class="dashboard__username">José</p>
              <p class="dashboard__role">Employé</p>
            </div>
          </div>

          <nav class="dashboard__nav" aria-label="Navigation espace employé">
            <ul class="dashboard__nav-list">
              <li>
                <a
                  href="#commandes"
                  class="dashboard__nav-link dashboard__nav-link--active"
                  >Commandes</a
                >
              </li>
              <li>
                <a href="#menus" class="dashboard__nav-link">Menus & Plats</a>
              </li>
              <li>
                <a href="#horaires" class="dashboard__nav-link">Horaires</a>
              </li>
              <li>
                <a href="#avis" class="dashboard__nav-link">Avis clients</a>
              </li>
            </ul>
          </nav>
        </aside>

        <!-- CONTENU -->
        <div class="dashboard__content">
          <!-- COMMANDES -->
          <section class="dashboard__section" id="commandes">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Commandes</h1>
            </div>

            <!-- Filtres commandes -->
            <div class="employe-filters">
              <div class="form-group">
                <label class="form-label" for="filtre-statut">Statut</label>
                <select id="filtre-statut" class="filters__select">
                  <option value="">Tous les statuts</option>
                  <option value="en-attente">En attente</option>
                  <option value="accepte">Acceptée</option>
                  <option value="en-preparation">En préparation</option>
                  <option value="en-livraison">En cours de livraison</option>
                  <option value="livre">Livrée</option>
                  <option value="retour-materiel">Retour matériel</option>
                  <option value="terminee">Terminée</option>
                  <option value="annulee">Annulée</option>
                </select>
              </div>
              <div class="space-sm"></div>
              <div class="form-group">
                <label class="form-label" for="filtre-client">Client</label>
                <input
                  type="text"
                  id="filtre-client"
                  class="form-input"
                  placeholder="Nom ou email..."
                />
              </div>
            </div>

            <!-- Liste commandes -->
            <div class="employe-commandes">
              <article class="commande-card">
                <div class="commande-card__header">
                  <div>
                    <span class="commande-card__id">#CMD-2025-001</span>
                    <h2 class="commande-card__menu">
                      Menu des Fêtes — Julie Dupont
                    </h2>
                  </div>
                  <span
                    class="commande-card__status commande-card__status--en-attente"
                    >En attente</span
                  >
                </div>

                <div class="commande-card__infos">
                  <div class="commande-card__info">
                    <span class="commande-card__info-label"
                      >Date livraison</span
                    >
                    <span>25/12/2025 à 12h00</span>
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

                <!-- Actions employé -->
                <div class="commande-card__employe-actions">
                  <div class="form-group">
                    <label class="form-label" for="statut-1"
                      >Changer le statut</label
                    >
                    <select
                      id="statut-1"
                      class="filters__select"
                      data-commande-id="1"
                    >
                      <option value="en-attente" selected>En attente</option>
                      <option value="accepte">Acceptée</option>
                      <option value="en-preparation">En préparation</option>
                      <option value="en-livraison">
                        En cours de livraison
                      </option>
                      <option value="livre">Livrée</option>
                      <option value="retour-materiel">
                        En attente retour matériel
                      </option>
                      <option value="terminee">Terminée</option>
                    </select>
                  </div>
                  <div class="space-sm"></div>
                  <button
                    type="button"
                    class="btn btn--primary btn--sm"
                    data-commande-id="1"
                  >
                    Mettre à jour
                  </button>
                  <button
                    type="button"
                    class="btn btn--secondary btn--sm"
                    data-annuler-id="1"
                  >
                    Annuler la commande
                  </button>
                </div>
              </article>
            </div>
          </section>

          <!-- MENUS & PLATS -->
          <section class="dashboard__section" id="menus">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Menus &amp; Plats</h1>
              <a href="php/menu/create.php" class="btn btn--primary btn--sm"
                >+ Nouveau menu</a
              >
            </div>

            <table class="employe-table">
              <thead>
                <tr>
                  <th>Menu</th>
                  <th>Thème</th>
                  <th>Prix</th>
                  <th>Stock</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- PHP : boucle sur les menus -->
                <tr>
                  <td>Menu des Fêtes</td>
                  <td><span class="menu-card__tag">Noël</span></td>
                  <td>45€</td>
                  <td>5</td>
                  <td class="employe-table__actions">
                    <a
                      href="php/menu/edit.php?id=1"
                      class="btn btn--secondary btn--sm"
                      >Modifier</a
                    >
                    <button
                      type="button"
                      class="btn btn--sm btn--primary"
                      data-id="1"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>Menu Tradition</td>
                  <td><span class="menu-card__tag">Classique</span></td>
                  <td>35€</td>
                  <td>8</td>
                  <td class="employe-table__actions">
                    <a
                      href="php/menu/edit.php?id=2"
                      class="btn btn--secondary btn--sm"
                      >Modifier</a
                    >
                    <button
                      type="button"
                      class="btn btn--sm btn--primary"
                      data-id="2"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </section>

          <!-- HORAIRES -->
          <section class="dashboard__section" id="horaires">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Horaires</h1>
            </div>

            <form
              class="auth-form"
              action="php/horaires/update.php"
              method="POST"
            >
              <table class="employe-table">
                <thead>
                  <tr>
                    <th>Jour</th>
                    <th>Ouverture</th>
                    <th>Fermeture</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Lundi</td>
                    <td>
                      <input
                        type="time"
                        name="lundi_ouverture"
                        class="form-input"
                        value="09:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="lundi_fermeture"
                        class="form-input"
                        value="18:00"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>Mardi</td>
                    <td>
                      <input
                        type="time"
                        name="mardi_ouverture"
                        class="form-input"
                        value="09:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="mardi_fermeture"
                        class="form-input"
                        value="18:00"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>Mercredi</td>
                    <td>
                      <input
                        type="time"
                        name="mercredi_ouverture"
                        class="form-input"
                        value="09:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="mercredi_fermeture"
                        class="form-input"
                        value="18:00"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>Jeudi</td>
                    <td>
                      <input
                        type="time"
                        name="jeudi_ouverture"
                        class="form-input"
                        value="09:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="jeudi_fermeture"
                        class="form-input"
                        value="18:00"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>Vendredi</td>
                    <td>
                      <input
                        type="time"
                        name="vendredi_ouverture"
                        class="form-input"
                        value="09:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="vendredi_fermeture"
                        class="form-input"
                        value="18:00"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>Samedi</td>
                    <td>
                      <input
                        type="time"
                        name="samedi_ouverture"
                        class="form-input"
                        value="10:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="samedi_fermeture"
                        class="form-input"
                        value="16:00"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>Dimanche</td>
                    <td>
                      <input
                        type="time"
                        name="dimanche_ouverture"
                        class="form-input"
                        value="10:00"
                      />
                    </td>
                    <td>
                      <input
                        type="time"
                        name="dimanche_fermeture"
                        class="form-input"
                        value="13:00"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="space-sm"></div>
              <button type="submit" class="btn btn--primary">
                Enregistrer les horaires
              </button>
            </form>
          </section>

          <!-- AVIS CLIENTS -->
          <section class="dashboard__section" id="avis">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Avis clients</h1>
            </div>

            <div class="avis-moderation">
              <div class="avis-moderation__card">
                <div class="avis-moderation__header">
                  <div>
                    <span class="avis-card__stars">★★★★★</span>
                    <p class="avis-moderation__author">
                      Marie L. — Menu des Fêtes
                    </p>
                  </div>
                  <span
                    class="commande-card__status commande-card__status--en-attente"
                    >En attente</span
                  >
                </div>
                <blockquote class="avis-card__text">
                  "Un grand merci à Julie et José ! Tout était délicieux."
                </blockquote>
                <div class="avis-moderation__actions">
                  <button
                    type="button"
                    class="btn btn--primary btn--sm"
                    data-avis-id="1"
                    data-action="valider"
                  >
                    Valider
                  </button>
                  <button
                    type="button"
                    class="btn btn--secondary btn--sm"
                    data-avis-id="1"
                    data-action="refuser"
                  >
                    Refuser
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>
