<?php
require_once __DIR__ . '/assets/php/config/db.php';
$pdo = getDB();

// 3 menus aléatoires avec stock dispo + image du plat principal
$menus = $pdo->query('
    SELECT m.menu_id, m.titre, m.prix_base, m.nombre_personne_min,
           t.libelle AS theme,
           (SELECT p.image_path 
            FROM compose_menu cm 
            JOIN plat p ON cm.plat_id = p.plat_id 
            WHERE cm.menu_id = m.menu_id 
              AND p.type = \'plat\' 
            LIMIT 1) AS image_path
    FROM menu m
    LEFT JOIN theme t ON m.theme_id = t.theme_id
    WHERE m.stock_disponible > 0
    ORDER BY RAND()
    LIMIT 3
')->fetchAll();

// Avis validés uniquement
$avis = $pdo->query('
    SELECT a.note, a.commentaire, u.prenom, u.nom
    FROM avis a
    JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
    WHERE a.est_valide = 1
    ORDER BY a.date_publication DESC
    LIMIT 3
')->fetchAll();

$title = 'Accueil';
$description = 'Vite & Gourmand, traiteur bordelais depuis 1999...';
ob_start();
?>
    <!-- ================================
      HERO
      ================================ -->
      <section class="hero">
        <div class="hero__container">
          <div class="hero__content">
            <div class="hero__badge">
              <span class="hero__badge-dot"></span>
              Traiteur bordelais depuis 1999
            </div>

            <h1 class="hero__title">
              La cuisine de Julie &amp; José,<br />
              <em>pour chaque occasion.</em>
            </h1>

            <p class="hero__sub">
              Menus faits maison livrés à Bordeaux et alentours — de Noël à
              Pâques, anniversaires et repas d'entreprise.
            </p>

            <div class="hero__actions">
              <a href="menus.php" class="btn btn--primary">Voir nos menus</a>
              <a href="contact.php" class="btn btn--secondary"
                >Nous contacter</a
              >
            </div>
          </div>

          <div class="hero__visual">
            <img
              src="assets/img/hero-img.jpg"
              alt="Plat traiteur Vite & Gourmand"
              class="hero__img"
            />
          </div>
        </div>
      </section>

      <!-- ================================
      STATS
      ================================ -->
      <section class="stats" aria-label="Chiffres clés">
        <div class="stats__grid">
          <div class="stats__item">
            <span class="stats__number">25+</span>
            <span class="stats__label">Années d'expérience</span>
          </div>
          <div class="stats__item">
            <span class="stats__number">500+</span>
            <span class="stats__label">Événements réalisés</span>
          </div>
          <div class="stats__item">
            <span class="stats__number">98%</span>
            <span class="stats__label">Clients satisfaits</span>
          </div>
          <div class="stats__item">
            <span class="stats__number">24h</span>
            <span class="stats__label">Délai de commande</span>
          </div>
        </div>
      </section>

      <!-- ================================
      SAVOIR-FAIRE
      ================================ -->
      <section class="savoir-faire" aria-labelledby="savoir-faire-title">
        <div class="savoir-faire__container">
          <div class="section-head">
            <span class="section-eyebrow">Ce qui nous différencie</span>
            <h2 class="section-title" id="savoir-faire-title">
              Notre savoir-faire
            </h2>
            <div class="section-line" aria-hidden="true"></div>
          </div>

          <div class="savoir-faire__grid">
            <article class="pillar">
              <div class="pillar__icon" aria-hidden="true">🌿</div>
              <h3 class="pillar__title">Produits frais &amp; locaux</h3>
              <p class="pillar__text">
                Partenariats avec des producteurs girondins pour une fraîcheur
                et une qualité irréprochables.
              </p>
            </article>

            <article class="pillar">
              <div class="pillar__icon" aria-hidden="true">👨‍🍳</div>
              <h3 class="pillar__title">Tout fait maison</h3>
              <p class="pillar__text">
                Chaque plat préparé dans notre laboratoire aux normes HACCP.
                Aucun produit industriel.
              </p>
            </article>

            <article class="pillar">
              <div class="pillar__icon" aria-hidden="true">✦</div>
              <h3 class="pillar__title">Sur mesure</h3>
              <p class="pillar__text">
                Adaptation à vos allergènes, régimes, budget et type
                d'événement. Un service 100% personnalisé.
              </p>
            </article>
          </div>
        </div>
      </section>

      <!-- ================================
      MENUS DU MOMENT
      ================================ -->
      <section class="menus-home" aria-labelledby="menus-home-title">
        <div class="menus-home__container">
          <div class="menus-home__header">
            <h2 class="section-title" id="menus-home-title">
              Nos menus du moment
            </h2>
            <a href="menus.html" class="menus-home__link"
              >Voir tous les menus →</a
            >
          </div>

          <div class="menus-home__grid">
            <?php if (empty($menus)): ?>
                <p>Aucun menu disponible pour le moment.</p>
            <?php else: ?>
              <?php foreach ($menus as $menu): ?>
                  <article class="menu-card">
                      <div class="menu-card__img-wrapper">
                          <img
                              src="<?= !empty($menu['image_path']) 
                                  ? 'assets/img/plats/' . htmlspecialchars($menu['image_path']) 
                                  : 'assets/img/menu-placeholder.jpg' ?>"
                              alt="<?= htmlspecialchars($menu['titre']) ?>"
                              class="menu-card__img"
                          />
                      </div>
                      <div class="menu-card__body">
                          <span class="menu-card__tag"><?= htmlspecialchars($menu['theme'] ?? '—') ?></span>
                          <h3 class="menu-card__title"><?= htmlspecialchars($menu['titre']) ?></h3>
                          <p class="menu-card__info">
                              À partir de <?= $menu['prix_base'] ?>€ · <?= $menu['nombre_personne_min'] ?> personnes min.
                          </p>
                          <a href="menu-detail.php?id=<?= $menu['menu_id'] ?>" class="btn btn--outline">
                              Voir le détail
                          </a>
                      </div>
                  </article>
                  <?php endforeach; ?>
              <?php endif; ?>
            </div>
        </div>
      </section>

      <!-- ================================
      AVIS CLIENTS
      ================================ -->
      <section class="avis" aria-labelledby="avis-title">
        <div class="avis__container">
          <div class="section-head">
            <span class="section-eyebrow">Ce qu'ils en pensent</span>
            <h2 class="section-title" id="avis-title">Avis clients</h2>
            <div class="section-line" aria-hidden="true"></div>
          </div>

          <div class="avis__grid">
              <?php if (empty($avis)): ?>
                  <p>Aucun avis pour le moment.</p>
              <?php else: ?>
                  <?php foreach ($avis as $a): ?>
                  <article class="avis-card">
                      <div class="avis-card__stars" aria-label="<?= $a['note'] ?> étoiles sur 5">
                          <?= str_repeat('★', $a['note']) ?><?= str_repeat('☆', 5 - $a['note']) ?>
                      </div>
                      <blockquote class="avis-card__text">
                          "<?= htmlspecialchars($a['commentaire']) ?>"
                      </blockquote>
                      <footer class="avis-card__author">
                          <?= htmlspecialchars($a['prenom'] . ' ' . strtoupper(substr($a['nom'], 0, 1)) . '.') ?>
                      </footer>
                  </article>
                  <?php endforeach; ?>
              <?php endif; ?>
          </div>
        </div>
      </section>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>
