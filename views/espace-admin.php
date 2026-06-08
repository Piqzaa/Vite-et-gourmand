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
          <section class="dashboard__section" id="stats">
            <h2 class="dashboard__section-title">Tableau de bord</h2>

            <!-- Chiffre d'affaires -->
            <div class="admin-ca">
              <div class="admin-ca__card">
                <p class="admin-ca__label">CA total</p>
                <p class="admin-ca__value" id="ca-total"><?= number_format($statsCA['total_ttc'] ?? 0, 2) ?> €</p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Commandes totales</p>
                <p class="admin-ca__value" id="commandes-total"><?= $statsCA['nb_commandes'] ?? 0 ?></p>
              </div>
            </div>

            <!-- Graphique -->
            <div class="admin-chart-container">
              <canvas id="adminChart" data-stats='<?= json_encode($statsParMenu) ?>'></canvas>
            </div>
          </section>

          <!-- COMMANDES -->
          <section class="dashboard__section" id="commandes">
            <h2 class="dashboard__section-title">Toutes les commandes</h2>
            <div class="employe-commandes">
              <?php foreach ($commandes as $cmd): ?>
                  <div class="space-sm"></div>
                  <article class="commande-card">
                      <div class="commande-card__header">
                          <div>
                              <span class="commande-card__id">#CMD-<?= $cmd['commande_id'] ?></span>
                              <h2 class="commande-card__menu"><?= htmlspecialchars($cmd['menu_nom']) ?> — <?= htmlspecialchars($cmd['client_prenom'] . ' ' . $cmd['client_nom']) ?></h2>
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
                              <span class="commande-card__info-label">Total</span>
                              <span class="commande-card__price"><?= number_format($cmd['prix_total_ttc'], 2) ?>€</span>
                          </div>
                      </div>
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
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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
                    <td><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></td>
                    <td><?= htmlspecialchars($emp['email']) ?></td>
                    <td>
                        <span class="commande-card__status <?= $emp['actif'] ? 'commande-card__status--accepte' : 'commande-card__status--annulee' ?>">
                            <?= $emp['actif'] ? 'Actif' : 'Désactivé' ?>
                        </span>
                    </td>
                    <td>
                        <form action="index.php?page=espace-admin&action=toggle-employe" method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="employe_id" value="<?= $emp['utilisateur_id'] ?>">
                            <input type="hidden" name="actif" value="<?= $emp['actif'] ? 0 : 1 ?>">
                            <button type="submit" class="btn btn--secondary btn--sm">
                                <?= $emp['actif'] ? 'Désactiver' : 'Réactiver' ?>
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
                    <td><?= htmlspecialchars($menu['titre']) ?></td>
                    <td><span class="menu-card__tag"><?= htmlspecialchars($menu['theme'] ?? '—') ?></span></td>
                    <td><?= $menu['prix_base'] ?>€</td>
                    <td><?= $menu['stock_disponible'] ?></td>
                    <td class="employe-table__actions">
                        <a href="index.php?page=menu-edit&id=<?= $menu['menu_id'] ?>" class="btn btn--secondary btn--sm">Modifier</a>
                        <form action="index.php?page=espace-admin&action=delete-menu" method="POST"
                              onsubmit="return confirm('Supprimer ce menu ?')">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="menu_id" value="<?= $menu['menu_id'] ?>">
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
                        <td><?= htmlspecialchars($plat['libelle']) ?></td>
                        <td><?= ucfirst($plat['type']) ?></td>
                        <td>
                            <?php if (!empty($plat['allergenes'])): ?>
                                <?php foreach ($plat['allergenes'] as $a): ?>
                                    <span class="allergene"><?= htmlspecialchars($a) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="index.php?page=espace-admin&action=delete-plat" method="POST" 
                                  onsubmit="return confirm('Supprimer ce plat ? Il sera retiré de tous les menus associés.')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="plat_id" value="<?= $plat['plat_id'] ?>">
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
              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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
                              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                              <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                              <input type="hidden" name="action" value="valider">
                              <button type="submit" class="btn btn--primary btn--sm">Valider</button>
                          </form>
                          <form action="index.php?page=espace-admin&action=moderer-avis" method="POST">
                              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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
require_once __DIR__ . '/../includes/layout.php';
?>
