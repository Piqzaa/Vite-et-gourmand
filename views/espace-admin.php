<?php
ob_start();
?>
      <div class="dashboard">
        <!-- SIDEBAR -->
        <aside class="dashboard__sidebar">
          <div class="dashboard__user">
            <div class="dashboard__avatar"><?= strtoupper(substr($_SESSION['user_prenom'], 0, 1) . substr($_SESSION['user_nom'], 0, 1)) ?></div>
            <div>
              <p class="dashboard__username"><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></p>
              <p class="dashboard__role">Administrateur</p>
            </div>
          </div>
          <nav class="dashboard__nav" aria-label="Navigation administration">
            <ul class="dashboard__nav-list">
              <li>
                <a href="#stats" class="dashboard__nav-link dashboard__nav-link--active">Statistiques</a>
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

        <!-- CONTENU -->
        <div class="dashboard__content">
          <!-- STATISTIQUES -->
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
                    <?php foreach ($menus as $m): ?>
                        <option value="<?= $m->getId() ?>"><?= htmlspecialchars($m->getTitre()) ?></option>
                    <?php endforeach; ?>
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
                <p class="admin-ca__value" id="ca-total"><?= number_format($statsCA['total_ttc'] ?? 0, 2) ?> €</p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Commandes totales</p>
                <p class="admin-ca__value" id="commandes-total"><?= $statsCA['nombre_commandes'] ?? 0 ?></p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Panier moyen</p>
                <p class="admin-ca__value" id="panier-moyen"><?= number_format($statsCA['panier_moyen'] ?? 0, 2) ?> €</p>
              </div>
            </div>

            <!-- Graphique commandes par menu (données SQL) -->
            <div class="admin-chart">
              <h2 class="admin-chart__title">Commandes par menu</h2>
              <canvas id="chart-commandes" 
                data-stats='<?= json_encode([
                    "labels" => array_column($statsParMenu, "titre"),
                    "commandes" => array_column($statsParMenu, "nombre_commandes"),
                    "ca" => array_column($statsParMenu, "ca")
                ]) ?>' 
                height="80">
            </canvas>
            </div>

              <div class="admin-chart__fallback">
                <table class="admin-chart__fallback-table">
                  <tbody>
                    <?php foreach ($statsParMenu as $stat): ?>
                    <tr>
                      <td><?= htmlspecialchars($stat['titre']) ?></td>
                      <td><?= $stat['nombre_commandes'] ?> cmd. — <?= number_format($stat['ca'], 2) ?>€</td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

          </section>

          <!-- COMMANDES -->
          <section class="dashboard__section" id="commandes">
            <div class="dashboard__section-header">
              <h2 class="dashboard__section-title">Commandes</h2>
            </div>

            <!-- Filtres commandes -->
            <div class="employe-filters">
              <div class="form-group">
                <label class="form-label" for="filtre-statut">Statut</label>
                <select id="filtre-statut" class="filters__select">
                  <option value="">Tous les statuts</option>
                  <option value="en attente">En attente</option>
                  <option value="accepté">Acceptée</option>
                  <option value="en préparation">En préparation</option>
                  <option value="en cours de livraison">En cours de livraison</option>
                  <option value="livré">Livrée</option>
                  <option value="en attente du retour de matériel">Retour matériel</option>
                  <option value="terminée">Terminée</option>
                  <option value="annulée">Annulée</option>
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
              <?php foreach ($commandes as $cmd): ?>
                  <div class="space-sm"></div>
                  <article class="commande-card" data-statut="<?= $cmd['statut'] ?>" data-client="<?= htmlspecialchars(strtolower($cmd['client_nom'] . ' ' . $cmd['client_email'])) ?>">
                      <div class="commande-card__header">
                          <div>
                              <span class="commande-card__id">#CMD-<?= $cmd['commande_id'] ?></span>
                              <h2 class="commande-card__menu">
                                  <?= htmlspecialchars($cmd['menu_nom']) ?> — 
                                  <?= htmlspecialchars($cmd['client_prenom'] . ' ' . $cmd['client_nom']) ?>
                              </h2>
                          </div>
                          <span class="commande-card__status"><?= ucfirst($cmd['statut']) ?></span>
                      </div>

                      <div class="commande-card__infos">
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">Email client</span>
                              <span><?= htmlspecialchars($cmd['client_email']) ?></span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">GSM client</span>
                              <span><?= htmlspecialchars($cmd['client_gsm']) ?></span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">Date prestation</span>
                              <span><?= date('d/m/Y', strtotime($cmd['date_prestation'])) ?> à <?= $cmd['heure_prestation'] ?></span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">Adresse</span>
                              <span><?= htmlspecialchars($cmd['adresse_livraison']) ?></span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">Personnes</span>
                              <span><?= $cmd['nombre_personnes'] ?> pers.</span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">Total</span>
                              <span class="commande-card__price"><?= number_format($cmd['prix_total_ttc'], 2) ?>€</span>
                          </div>
                      </div>

                      <?php if ($cmd['statut'] !== 'annulée' && $cmd['statut'] !== 'terminée'): ?>
                      <div>
                          <form action="index.php?page=espace-admin&action=update-commande-statut" method="POST">
                              <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                              <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                              <div class="form-group">
                                  <label class="form-label">Changer le statut</label>
                                  <select name="statut" class="filters__select">
                                      <?php
                                      $statuts = ['en attente', 'accepté', 'en préparation', 'en cours de livraison', 'livré', 'en attente du retour de matériel', 'terminée'];
                                      foreach ($statuts as $s):
                                      ?>
                                      <option value="<?= $s ?>" <?= $cmd['statut'] === $s ? 'selected' : '' ?>>
                                          <?= ucfirst($s) ?>
                                      </option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                              <div class="space-sm"></div>
                              <button type="submit" class="btn btn--primary btn--sm">Mettre à jour</button>
                          </form>

                          <form action="index.php?page=espace-admin&action=annuler-commande" method="POST" 
                                onsubmit="return confirm('Annuler cette commande ?')">
                              <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                              <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                              <div class="form-group">
                                  <label class="form-label">Motif d'annulation</label>
                                  <textarea name="motif" class="form-input" rows="2" 
                                            placeholder="Expliquez le motif..." required></textarea>
                              </div>
                              <div class="space-sm"></div>
                              <button type="submit" class="btn btn--secondary btn--sm">Annuler la commande</button>
                          </form>
                      </div>
                      <?php endif; ?>
                  </article>
              <?php endforeach; ?>
              </div>
          </section>

          <!-- EMPLOYÉS -->
          <section class="dashboard__section" id="employes">
            <div class="dashboard__section-header">
              <h2 class="dashboard__section-title">Gestion des employés</h2>
              <button class="btn btn--primary btn--sm" id="btn-add-employe">
                + Nouvel employé
              </button>
            </div>

            <!-- Formulaire ajout (masqué par défaut) -->
            <div class="admin-form-box" id="form-employe" style="display: none">
              <form action="index.php?page=espace-admin&action=create-employe" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="prenom">Prénom</label>
                    <input type="text" name="prenom" class="form-input" required />
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="nom">Nom</label>
                    <input type="text" name="nom" class="form-input" required />
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="email">Email professionnel</label>
                  <input type="email" name="email" class="form-input" required />
                </div>
                <div class="form-group">
                  <label class="form-label" for="password">Mot de passe temporaire</label>
                  <input type="password" name="password" class="form-input" required />
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
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($employes as $emp): ?>
                <tr>
                    <td><?= htmlspecialchars($emp->getPrenom() . ' ' . $emp->getNom()) ?></td>
                    <td><?= htmlspecialchars($emp->getEmail()) ?></td>
                    <td>
                        <span class="commande-card__status <?= $emp->isActif() ? 'commande-card__status--accepte' : 'commande-card__status--annulee' ?>">
                            <?= $emp->isActif() ? 'Actif' : 'Désactivé' ?>
                        </span>
                    </td>
                    <td>
                        <form action="index.php?page=espace-admin&action=toggle-employe" method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                            <input type="hidden" name="employe_id" value="<?= $emp->getId() ?>">
                            <input type="hidden" name="actif" value="<?= $emp->isActif() ? 0 : 1 ?>">
                            <button type="submit" class="btn btn--secondary btn--sm">
                                <?= $emp->isActif() ? 'Désactiver' : 'Réactiver' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </section>

          <!-- MENUS & PLATS -->
          <section class="dashboard__section" id="menus">
            <div class="dashboard__section-header">
              <h2 class="dashboard__section-title">Menus &amp; Plats</h2>
              <a href="index.php?page=menu-create" class="btn btn--primary btn--sm">+ Nouveau menu</a>
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
                <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= htmlspecialchars($menu->getTitre()) ?></td>
                    <td><span class="menu-card__tag"><?= htmlspecialchars($menu->getThemeLabel() ?? '—') ?></span></td>
                    <td><?= $menu->getPrixBase() ?>€</td>
                    <td><?= $menu->getStockDisponible() ?></td>
                    <td>
                        <a href="index.php?page=menu-edit&id=<?= $menu->getId() ?>" class="btn btn--secondary btn--sm">Modifier</a>
                        <div class="space-sm"></div>
                        <form action="index.php?page=espace-admin&action=delete-menu" method="POST"
                              onsubmit="return confirm('Supprimer ce menu ?')">
                            <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                            <input type="hidden" name="menu_id" value="<?= $menu->getId() ?>">
                            <button type="submit" class="btn btn--sm btn--primary">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="space-md"></div>
            <div class="dashboard__section-header">
            <h2 class="dashboard__section-title">Plats</h2>
            <a href="index.php?page=plat-create" class="btn btn--primary btn--sm">+ Nouveau plat</a>
            </div>
            <table class="employe-table">
                <thead>
                    <tr>
                        <th>Plat</th>
                        <th>Type</th>
                        <th>Allergènes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($platsAvecAllergenes as $plat): ?>
                    <tr>
                        <td><?= htmlspecialchars($plat->getLibelle()) ?></td>
                        <td><?= ucfirst($plat->getType()) ?></td>
                        <td>
                            <?php if (!empty($plat->getAllergenes())): ?>
                                <?php foreach ($plat->getAllergenes() as $a): ?>
                                    <span class="allergene"><?= htmlspecialchars($a) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="index.php?page=espace-admin&action=delete-plat" method="POST" 
                                  onsubmit="return confirm('Supprimer ce plat ? Il sera retiré de tous les menus associés.')">
                                <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                                <input type="hidden" name="plat_id" value="<?= $plat->getId() ?>">
                                <button type="submit" class="btn btn--sm btn--primary">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
          </section>

          <!-- HORAIRES -->
          <section class="dashboard__section" id="horaires">
            <div class="dashboard__section-header">
              <h2 class="dashboard__section-title">Horaires</h2>
            </div>

            <form class="auth-form" action="index.php?page=espace-admin&action=update-horaires" method="POST">
              <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
              <table class="employe-table">
                <thead>
                  <tr>
                    <th>Jour</th>
                    <th>Ouverture</th>
                    <th>Fermeture</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                  foreach ($horaires as $i => $h): 
                  ?>
                  <tr>
                      <td><?= $jours[$i] ?></td>
                      <td>
                          <input type="time" name="horaire[<?= $h['horaire_id'] ?>][ouverture]" 
                                class="form-input" value="<?= $h['heure_ouverture'] ?>" />
                      </td>
                      <td>
                          <input type="time" name="horaire[<?= $h['horaire_id'] ?>][fermeture]" 
                                class="form-input" value="<?= $h['heure_fermeture'] ?>" />
                      </td>
                  </tr>
                  <?php endforeach; ?>
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
              <h2 class="dashboard__section-title">Avis clients</h2>
            </div>

            <div class="avis-moderation">
              <?php if (empty($avis)): ?>
                  <p>Aucun avis en attente de modération.</p>
              <?php else: ?>
                  <?php foreach ($avis as $a): ?>
                  <div class="avis-moderation__card">
                      <div class="avis-moderation__header">
                          <div>
                              <span class="avis-card__stars"><?= str_repeat('★', $a['note']) ?></span>
                              <p class="avis-moderation__author">
                                  <?= htmlspecialchars($a['client_prenom'] . ' ' . $a['client_nom']) ?> — 
                                  <?= htmlspecialchars($a['menu_titre']) ?>
                              </p>
                          </div>
                          <span class="commande-card__status commande-card__status--en-attente">En attente</span>
                      </div>
                      <blockquote class="avis-card__text">
                          "<?= htmlspecialchars($a['commentaire']) ?>"
                      </blockquote>
                      <div class="avis-moderation__actions">
                          <form action="index.php?page=espace-admin&action=moderer-avis" method="POST">
                              <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                              <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                              <input type="hidden" name="action" value="valider">
                              <button type="submit" class="btn btn--primary btn--sm">Valider</button>
                          </form>
                          <form action="index.php?page=espace-admin&action=moderer-avis" method="POST">
                              <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                              <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                              <input type="hidden" name="action" value="refuser">
                              <button type="submit" class="btn btn--secondary btn--sm">Refuser</button>
                          </form>
                      </div>
                  </div>
                  <?php endforeach; ?>
              <?php endif; ?>
              </div>
          </section>
        </div>
      </div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout/main.php';
?>
