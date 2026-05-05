<?php
$title = 'Mentions légales';
$description = 'Découvrez les mentions légales de Vite & Gourmand, incluant les informations sur l\'éditeur du site, l\'hébergement, la propriété intellectuelle, les données personnelles (RGPD), les cookies et la responsabilité.';
ob_start();
?>
      <section class="page-header">
        <div class="page-header__container">
          <h1 class="page-header__title">Mentions légales</h1>
        </div>
      </section>

      <section class="legal-page">
        <div class="legal-page__container">
          <div class="legal-content">
            <h2 class="legal-content__title">Éditeur du site</h2>
            <p>Le site viteetgourmand.fr est édité par :</p>
            <ul>
              <li><strong>Raison sociale :</strong> Vite &amp; Gourmand</li>
              <li>
                <strong>Forme juridique :</strong> Entreprise individuelle
              </li>
              <li>
                <strong>Adresse :</strong> 27 Rue des Faures, 33000 Bordeaux
              </li>
              <li><strong>Téléphone :</strong> 05 56 87 42 13</li>
              <li><strong>Email :</strong> contact@viteetgourmand.com</li>
              <li><strong>Gérants :</strong> Julie et José</li>
            </ul>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Hébergement</h2>
            <p>Le site est hébergé par :</p>
            <ul>
              <li><strong>Société :</strong> Fly.io</li>
              <li><strong>Site web :</strong> https://fly.io</li>
            </ul>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Propriété intellectuelle</h2>
            <p>
              L'ensemble des contenus présents sur ce site (textes, images,
              logos, graphismes) sont la propriété exclusive de Vite &amp;
              Gourmand et sont protégés par les lois en vigueur sur la propriété
              intellectuelle. Toute reproduction, distribution ou utilisation
              sans autorisation préalable est strictement interdite.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Données personnelles (RGPD)</h2>
            <p>
              Conformément au Règlement Général sur la Protection des Données
              (RGPD) et à la loi Informatique et Libertés, vous disposez des
              droits suivants concernant vos données personnelles :
            </p>
            <ul>
              <li>Droit d'accès à vos données</li>
              <li>Droit de rectification</li>
              <li>Droit à l'effacement (droit à l'oubli)</li>
              <li>Droit à la portabilité</li>
              <li>Droit d'opposition au traitement</li>
            </ul>
            <p>
              Pour exercer ces droits, contactez-nous à l'adresse :
              <strong>contact@viteetgourmand.com</strong>
            </p>
            <p>
              Les données collectées sur ce site (nom, prénom, email, adresse,
              téléphone) sont utilisées uniquement dans le cadre de la gestion
              des commandes et de la relation client. Elles ne sont jamais
              transmises à des tiers à des fins commerciales.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Cookies</h2>
            <p>
              Ce site utilise des cookies techniques nécessaires à son bon
              fonctionnement (gestion des sessions). Aucun cookie publicitaire
              ou de tracking n'est utilisé.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Responsabilité</h2>
            <p>
              Vite &amp; Gourmand s'efforce de maintenir les informations
              publiées sur ce site à jour et exactes. Cependant, nous ne pouvons
              garantir l'exactitude, la complétude ou l'actualité des
              informations diffusées.
            </p>
          </div>
        </div>
      </section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>