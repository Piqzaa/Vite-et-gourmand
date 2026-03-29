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
            <div class="dashboard__avatar">AD</div>
            <div>
              <p class="dashboard__username">Administrateur</p>
              <p class="dashboard__role">Admin</p>
            </div>
          </div>

          <nav class="dashboard__nav" aria-label="Navigation espace admin">
            <ul class="dashboard__nav-list">
              <li>
                <a
                  href="#statistiques"
                  class="dashboard__nav-link dashboard__nav-link--active"
                  >Statistiques</a
                >
              </li>
              <li>
                <a href="#commandes" class="dashboard__nav-link">Commandes</a>
              </li>
              <li>
                <a href="#employes" class="dashboard__nav-link">Employés</a>
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

        <div class="dashboard__content">
          <!-- STATISTIQUES (données MongoDB) -->
          <section class="dashboard__section" id="statistiques">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Statistiques</h1>
            </div>

            <!-- Filtres CA -->
            <div class="admin-stats-filters">
              <div class="form-group">
                <label class="form-label" for="stats-menu"
                  >Filtrer par menu</label
                >
                <select id="stats-menu" class="filters__select">
                  <option value="">Tous les menus</option>
                  <!-- PHP : options dynamiques -->
                  <option value="1">Menu des Fêtes</option>
                  <option value="2">Menu Tradition</option>
                  <option value="3">Menu Printanier</option>
                </select>
              </div>
              <div class="form-group">
                <div class="space-sm"></div>
                <label class="form-label" for="stats-debut"
                  >Date de début</label
                >
                <input
                  type="date"
                  id="stats-debut"
                  name="date_debut"
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <div class="space-sm"></div>
                <label class="form-label" for="stats-fin">Date de fin</label>
                <input
                  type="date"
                  id="stats-fin"
                  name="date_fin"
                  class="form-input"
                />
              </div>
              <div class="space-sm"></div>
              <button
                type="button"
                class="btn btn--primary btn--sm"
                id="btn-filtrer-stats"
              >
                Filtrer
              </button>
            </div>

            <!-- Chiffre d'affaires -->
            <div class="admin-ca">
              <div class="admin-ca__card">
                <p class="admin-ca__label">CA total</p>
                <p class="admin-ca__value" id="ca-total">—</p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Commandes totales</p>
                <p class="admin-ca__value" id="commandes-total">—</p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Panier moyen</p>
                <p class="admin-ca__value" id="panier-moyen">—</p>
              </div>
            </div>

            <!-- Graphique commandes par menu (données MongoDB) -->
            <div class="admin-chart">
              <h2 class="admin-chart__title">Commandes par menu</h2>
              <canvas id="chart-commandes" height="80"></canvas>
            </div>
          </section>

          <!-- COMMANDES (même que employé) -->
          <section class="dashboard__section" id="commandes">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Commandes</h1>
            </div>

            <div class="employe-filters">
              <div class="form-group">
                <label class="form-label" for="filtre-statut-admin"
                  >Statut</label
                >
                <select id="filtre-statut-admin" class="filters__select">
                  <option value="">Tous les statuts</option>
                  <option value="en-attente">En attente</option>
                  <option value="accepte">Acceptée</option>
                  <option value="en-preparation">En préparation</option>
                  <option value="en-livraison">En cours de livraison</option>
                  <option value="terminee">Terminée</option>
                  <option value="annulee">Annulée</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="filtre-client-admin"
                  >Client</label
                >
                <input
                  type="text"
                  id="filtre-client-admin"
                  class="form-input"
                  placeholder="Nom ou email..."
                />
              </div>
            </div>

            <!-- PHP : boucle commandes -->
            <p class="dashboard__empty">Les commandes s'afficheront ici.</p>
          </section>

          <!-- GESTION EMPLOYÉS -->
          <section class="dashboard__section" id="employes">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Employés</h1>
            </div>

            <!-- Créer un employé -->
            <div class="admin-create-employe">
              <h2 class="admin-create-employe__title">
                Créer un compte employé
              </h2>
              <div class="space-sm"></div>
              <form
                class="auth-form"
                action="php/admin/create-employe.php"
                method="POST"
              >
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="employe-email"
                      >Email (username)</label
                    >
                    <input
                      type="email"
                      id="employe-email"
                      name="email"
                      class="form-input"
                      placeholder="employe@viteetgourmand.com"
                      required
                    />
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="employe-password"
                      >Mot de passe</label
                    >
                    <input
                      type="password"
                      id="employe-password"
                      name="password"
                      class="form-input"
                      placeholder="••••••••••"
                      required
                    />
                  </div>
                </div>
                <p class="form-hint">
                  Le mot de passe ne sera pas communiqué par email — l'employé
                  devra le récupérer auprès de l'administrateur.
                </p>
                <div class="space-sm"></div>
                <button type="submit" class="btn btn--primary btn--sm">
                  Créer le compte
                </button>
              </form>
            </div>

            <!-- Liste employés -->
            <table class="employe-table">
              <thead>
                <tr>
                  <th>Email</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- PHP : boucle employés -->
                <tr>
                  <td>jose@viteetgourmand.com</td>
                  <td>
                    <span
                      class="commande-card__status commande-card__status--accepte"
                      >Actif</span
                    >
                  </td>
                  <td class="employe-table__actions">
                    <button
                      type="button"
                      class="btn btn--secondary btn--sm"
                      data-employe-id="1"
                      data-action="desactiver"
                    >
                      Désactiver
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </section>

          <!-- MENUS & PLATS (même que employé) -->
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
                <!-- PHP : boucle menus -->
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
                      class="btn btn--sm employe-table__delete"
                      data-id="1"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </section>

          <!-- HORAIRES (même que employé) -->
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

          <!-- AVIS  -->
          <section class="dashboard__section" id="avis">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Avis clients</h1>
            </div>

            <div class="avis-moderation">
              <!-- PHP : boucle avis en attente -->
              <p class="dashboard__empty">
                Aucun avis en attente de modération.
              </p>
            </div>
          </section>
        </div>
      </div>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>