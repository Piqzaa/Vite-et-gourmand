<?php
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

sessionStart();

if (!isConnected() || !in_array(getUserRole(), ['employe', 'admin'])) {
    header('Location: ' . BASE_URL . '/connexion.php');
    exit;
}

$pdo = getDB();

// Commandes avec infos client et menu
$commandes = $pdo->query('
    SELECT c.*, 
          m.titre AS menu_nom,
          u.nom AS client_nom, 
          u.prenom AS client_prenom,
          u.email AS client_email,
          u.gsm AS client_gsm
    FROM commande c
    JOIN menu m ON c.menu_id = m.menu_id
    JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
    ORDER BY c.date_commande DESC
')->fetchAll();

// Menus
$menus = $pdo->query('
    SELECT m.menu_id, m.titre, m.prix_base, m.stock_disponible,
          t.libelle AS theme
    FROM menu m
    LEFT JOIN theme t ON m.theme_id = t.theme_id
    ORDER BY m.titre
')->fetchAll();

$plats = $pdo->query('
    SELECT plat_id,
    libelle, type FROM plat ORDER BY type, libelle
    ')->fetchAll();

// Allergènes par plat
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

// Horaires
$horaires = $pdo->query('
    SELECT * FROM horaire ORDER BY horaire_id ASC
')->fetchAll();

// Avis en attente
$avis = $pdo->query('
    SELECT a.*, 
          u.nom AS client_nom, 
          u.prenom AS client_prenom,
          m.titre AS menu_nom
    FROM avis a
    JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
    JOIN commande c ON a.commande_id = c.commande_id
    JOIN menu m ON c.menu_id = m.menu_id
    WHERE a.est_valide = 0
    ORDER BY a.date_publication DESC
')->fetchAll();

$title = 'Espace employé';
$description = 'Bienvenue dans votre espace employé sur Vite & Gourmand. Gérez les commandes, consultez les avis clients, et mettez à jour les horaires d\'ouverture pour offrir la meilleure expérience à nos clients à Bordeaux.';
ob_start();
?>
      <div class="dashboard">
        <!-- SIDEBAR -->
        <aside class="dashboard__sidebar">
          <div class="dashboard__user">
            <div class="dashboard__avatar">
              <?= strtoupper(substr($_SESSION['user_prenom'], 0, 1) . substr($_SESSION['user_nom'], 0, 1)) ?>
            </div>
            <div>
            <p class="dashboard__username"><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></p>
            <p class="dashboard__role"><?= ucfirst($_SESSION['user_role']) ?></p>
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
                      <div class="commande-card__employe-actions">
                          <form action="assets/php/commande/update-statut.php" method="POST">
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
                              <button type="submit" class="btn btn--secondary btn--sm">Annuler la commande</button>
                          </form>
                      </div>
                      <?php endif; ?>
                  </article>
              <?php endforeach; ?>
              </div>
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
                              <input type="hidden" name="avis_id" value="<?= $a['avis_id'] ?>">
                              <input type="hidden" name="action" value="valider">
                              <button type="submit" class="btn btn--primary btn--sm">Valider</button>
                          </form>
                          <form action="assets/php/avis/moderer.php" method="POST" style="display:inline">
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
