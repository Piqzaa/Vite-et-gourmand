<?php
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

sessionStart();

if (!isConnected() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo = getDB();

// Même requêtes que espace-employe.php
$commandes = $pdo->query('
    SELECT c.*, m.titre AS menu_nom,
           u.nom AS client_nom, u.prenom AS client_prenom,
           u.email AS client_email, u.gsm AS client_gsm
    FROM commande c
    JOIN menu m ON c.menu_id = m.menu_id
    JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
    ORDER BY c.date_commande DESC
')->fetchAll();

$menus = $pdo->query('
    SELECT m.menu_id, m.titre, m.prix_base, m.stock_disponible,
           t.libelle AS theme
    FROM menu m
    LEFT JOIN theme t ON m.theme_id = t.theme_id
    ORDER BY m.titre
')->fetchAll();

$horaires = $pdo->query('SELECT * FROM horaire ORDER BY horaire_id ASC')->fetchAll();

$avis = $pdo->query('
    SELECT a.*, u.nom AS client_nom, u.prenom AS client_prenom,
           m.titre AS menu_nom
    FROM avis a
    JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
    JOIN commande c ON a.commande_id = c.commande_id
    JOIN menu m ON c.menu_id = m.menu_id
    WHERE a.est_valide = 0
    ORDER BY a.date_publication DESC
')->fetchAll();

// Spécifique admin — liste des employés
$employes = $pdo->query('
    SELECT utilisateur_id, nom, prenom, email, actif
    FROM utilisateur
    WHERE role = "employe"
    ORDER BY nom
')->fetchAll();

// Spécifique admin — stats CA depuis MySQL
// (MongoDB pour le graphique, on y revient)
$statsCA = $pdo->query('
    SELECT 
        COUNT(*) AS nb_commandes,
        SUM(prix_total_ttc) AS ca_total,
        AVG(prix_total_ttc) AS panier_moyen
    FROM commande
    WHERE statut NOT IN ("annulée")
')->fetch();

// Stats par menu pour le graphique
$statsParMenu = $pdo->query('
    SELECT m.titre, COUNT(c.commande_id) AS nb_commandes, SUM(c.prix_total_ttc) AS ca
    FROM commande c
    JOIN menu m ON c.menu_id = m.menu_id
    WHERE c.statut NOT IN ("annulée")
    GROUP BY m.menu_id, m.titre
    ORDER BY nb_commandes DESC
')->fetchAll();

$plats = $pdo->query('SELECT plat_id, libelle, type FROM plat ORDER BY type, libelle')->fetchAll();
$platsAvecAllergenes = [];
foreach ($plats as $plat) {
    $stmt = $pdo->prepare('
        SELECT a.libelle FROM allergene a
        JOIN plat_allergene pa ON a.allergene_id = pa.allergene_id
        WHERE pa.plat_id = ?
    ');
    $stmt->execute([$plat['plat_id']]);
    $plat['allergenes'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $platsAvecAllergenes[] = $plat;
}

$title = 'Espace administrateur';
$description = 'Gérez les commandes, les employés, les menus et les avis clients depuis votre espace administrateur.';
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
                    <?php foreach ($menus as $m): ?>
                        <option value="<?= $m['menu_id'] ?>"><?= htmlspecialchars($m['titre']) ?></option>
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
                <p class="admin-ca__value" id="ca-total"><?= number_format($statsCA['ca_total'] ?? 0, 2) ?> €</p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Commandes totales</p>
                <p class="admin-ca__value" id="commandes-total"><?= $statsCA['nb_commandes'] ?? 0 ?></p>
              </div>
              <div class="admin-ca__card">
                <p class="admin-ca__label">Panier moyen</p>
                <p class="admin-ca__value" id="panier-moyen"><?= number_format($statsCA['panier_moyen'] ?? 0, 2) ?> €</p>
              </div>
            </div>

            <!-- Graphique commandes par menu (données MongoDB) -->
            <div class="admin-chart">
              <h2 class="admin-chart__title">Commandes par menu</h2>
              <canvas id="chart-commandes" 
                data-stats='<?= json_encode([
                    "labels" => array_column($statsParMenu, "titre"),
                    "commandes" => array_column($statsParMenu, "nb_commandes"),
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
                      <td><?= $stat['nb_commandes'] ?> cmd. — <?= number_format($stat['ca'], 2) ?>€</td>
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
                              <span class="commande-card__info-label">Date prestation</span>
                              <span><?= date('d/m/Y', strtotime($cmd['date_prestation'])) ?> à <?= $cmd['heure_prestation'] ?></span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">Adresse</span>
                              <span><?= htmlspecialchars($cmd['adresse_livraison']) ?></span>
                          </div>
                          <div class="commande-card__info">
                              <span class="commande-card__info-label">GSM client</span>
                              <span><?= htmlspecialchars($cmd['client_gsm']) ?></span>
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
                          <form action="assets/php/commande/update-statut.php" method="POST">
                              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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

                          <form action="assets/php/commande/annuler-employe.php" method="POST"
                                style="margin-top: 0.5rem"
                                onsubmit="return confirm('Annuler cette commande ?')">
                              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                              <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                              <div class="form-group">
                                  <label class="form-label">Mode de contact client</label>
                                  <select name="mode_contact" class="filters__select" required>
                                      <option value="gsm">Appel GSM</option>
                                      <option value="mail">Mail</option>
                                  </select>
                              </div>
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
                action="assets/php/admin/create-employe.php"
                method="POST"
              >
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="form-row">
                  <div class="form-group">
                      <label class="form-label" for="employe-nom">Nom</label>
                      <input type="text" id="employe-nom" name="nom" class="form-input" placeholder="Dupont" required />
                  </div>
                  <div class="form-group">
                      <label class="form-label" for="employe-prenom">Prénom</label>
                      <input type="text" id="employe-prenom" name="prenom" class="form-input" placeholder="Jean" required />
                  </div>
              </div>
              <div class="form-row">
                  <div class="form-group">
                      <label class="form-label" for="employe-email">Email (username)</label>
                      <input type="email" id="employe-email" name="email" class="form-input" placeholder="employe@viteetgourmand.com" required />
                  </div>
                  <div class="form-group">
                      <label class="form-label" for="employe-password">Mot de passe</label>
                      <input type="password" id="employe-password" name="password" class="form-input" placeholder="••••••••••" required />
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
                  <th>Activité</th>
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
                        <form action="assets/php/admin/toggle-employe.php" method="POST" style="display:inline">
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
              
              <a href="menu-create.php" class="btn btn--primary btn--sm">+ Nouveau menu</a>
              
            
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
                        <a href="menu-edit.php?id=<?= $menu['menu_id'] ?>" class="btn btn--secondary btn--sm">Modifier</a>
                        <form action="assets/php/menu/delete.php" method="POST" style="display:inline"
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
            <a href="plat-create.php" class="btn btn--primary btn--sm">+ Nouveau plat</a>
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
                            <form action="assets/php/plat/delete.php" method="POST"
                                  style="display:inline"
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

            <form
              class="auth-form"
              action="assets/php/horaires/update.php"
              method="POST"
            >
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
                                  <?= htmlspecialchars($a['menu_nom']) ?>
                              </p>
                          </div>
                          <span class="commande-card__status commande-card__status--en-attente">En attente</span>
                      </div>
                      <blockquote class="avis-card__text">
                          "<?= htmlspecialchars($a['commentaire']) ?>"
                      </blockquote>
                      <div class="avis-moderation__actions">
                          <form action="assets/php/avis/moderer.php" method="POST" style="display:inline">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
    <input type="hidden" name="action" value="valider">
                              <button type="submit" class="btn btn--primary btn--sm">Valider</button>
                          </form>
                          <form action="assets/php/avis/moderer.php" method="POST" style="display:inline">
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
require_once 'includes/layout.php';
?>