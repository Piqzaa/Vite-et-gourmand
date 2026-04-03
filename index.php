<?php
$title = 'Accueil';
$description = 'Vite & Gourmand, traiteur bordelais depuis 1999. Découvrez nos menus faits maison, adaptés à chaque occasion : Noël, Pâques, anniversaires, repas d\'entreprise. Commandez en ligne et régalez vos invités avec nos plats savoureux et notre service personnalisé.';
ob_start();
?>
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
              <a href="contact.html" class="btn btn--secondary"
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
            <!-- Sera généré dynamiquement via PHP -->

            <article class="menu-card">
              <div class="menu-card__img-wrapper">
                <img
                  src="assets/img/menu-placeholder.jpg"
                  alt="Menu des Fêtes"
                  class="menu-card__img"
                />
              </div>
              <div class="menu-card__body">
                <span class="menu-card__tag">Noël</span>
                <h3 class="menu-card__title">Menu des Fêtes</h3>
                <p class="menu-card__info">
                  À partir de 45€ · 8 personnes min.
                </p>
                <a href="menu-detail.html" class="btn btn--outline"
                  >Voir le détail</a
                >
              </div>
            </article>

            <article class="menu-card">
              <div class="menu-card__img-wrapper">
                <img
                  src="assets/img/menu-placeholder.jpg"
                  alt="Menu Tradition"
                  class="menu-card__img"
                />
              </div>
              <div class="menu-card__body">
                <span class="menu-card__tag">Classique</span>
                <h3 class="menu-card__title">Menu Tradition</h3>
                <p class="menu-card__info">
                  À partir de 35€ · 6 personnes min.
                </p>
                <a href="menu-detail.html" class="btn btn--outline"
                  >Voir le détail</a
                >
              </div>
            </article>

            <article class="menu-card">
              <div class="menu-card__img-wrapper">
                <img
                  src="assets/img/menu-placeholder.jpg"
                  alt="Menu Printanier"
                  class="menu-card__img"
                />
              </div>
              <div class="menu-card__body">
                <span class="menu-card__tag">Pâques</span>
                <h3 class="menu-card__title">Menu Printanier</h3>
                <p class="menu-card__info">
                  À partir de 40€ · 6 personnes min.
                </p>
                <a href="menu-detail.html" class="btn btn--outline"
                  >Voir le détail</a
                >
              </div>
            </article>
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
            <!-- Sera généré dynamiquement via PHP (avis validés uniquement) -->

            <article class="avis-card">
              <div class="avis-card__stars" aria-label="5 étoiles sur 5">
                ★★★★★
              </div>
              <blockquote class="avis-card__text">
                "Un grand merci à Julie et José ! Tout était délicieux, nos
                invités se sont régalés."
              </blockquote>
              <footer class="avis-card__author">Marie L. · Anniversaire</footer>
            </article>

            <article class="avis-card">
              <div class="avis-card__stars" aria-label="5 étoiles sur 5">
                ★★★★★
              </div>
              <blockquote class="avis-card__text">
                "Prestation parfaite, saveurs incroyables. On ressent la passion
                derrière chaque plat."
              </blockquote>
              <footer class="avis-card__author">
                Thomas &amp; Élodie · Baptême
              </footer>
            </article>

            <article class="avis-card">
              <div class="avis-card__stars" aria-label="5 étoiles sur 5">
                ★★★★★
              </div>
              <blockquote class="avis-card__text">
                "Professionnels et à l'écoute. Menu original et raffiné, tout le
                monde a adoré."
              </blockquote>
              <footer class="avis-card__author">
                Sophie D. · Repas d'entreprise
              </footer>
            </article>
          </div>
        </div>
      </section>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>
