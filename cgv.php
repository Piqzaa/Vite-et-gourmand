<?php
$title = 'Conditions générales de vente';
$description = 'Vite & Gourmand - Conditions générales de vente';
ob_start();
?>
      <section class="page-header">
        <div class="page-header__container">
          <h1 class="page-header__title">Conditions générales de vente</h1>
        </div>
      </section>

      <section class="legal-page">
        <div class="legal-page__container">
          <div class="legal-content">
            <h2 class="legal-section__title">Article 1 — Objet</h2>
            <p>
              Les présentes conditions générales de vente régissent les
              relations contractuelles entre Vite &amp; Gourmand (ci-après "le
              Prestataire") et tout client passant commande via le site
              viteetgourmand.fr ou directement auprès de l'entreprise.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Article 2 — Commandes</h2>
            <p>
              Toute commande doit être effectuée au minimum
              <strong>72 heures avant</strong> la date de la prestation, sauf
              accord préalable du Prestataire. La commande est confirmée après
              réception d'un email de confirmation de la part de Vite &amp;
              Gourmand.
            </p>
            <p>
              Le client s'engage à fournir des informations exactes lors de la
              commande (adresse de livraison, date, nombre de personnes). Toute
              modification après acceptation de la commande doit être signalée
              dans les meilleurs délais.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">
              Article 3 — Prix et facturation
            </h2>
            <p>
              Les prix indiqués sur le site sont en euros TTC. Le Prestataire se
              réserve le droit de modifier ses tarifs à tout moment. Les
              commandes sont facturées au tarif en vigueur au moment de la
              validation.
            </p>
            <p>
              Une réduction de <strong>10%</strong> est appliquée
              automatiquement pour toute commande dont le nombre de personnes
              dépasse de 5 ou plus le nombre minimum indiqué sur le menu.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Article 4 — Livraison</h2>
            <p>
              La livraison est <strong>gratuite</strong> pour toute prestation
              ayant lieu dans la ville de Bordeaux. Pour toute livraison en
              dehors de Bordeaux, des frais de livraison seront appliqués selon
              le barème suivant :
            </p>
            <ul>
              <li>Forfait de base : <strong>5€</strong></li>
              <li>
                Majoration kilométrique :
                <strong>0,59€ par kilomètre</strong> parcouru (aller)
              </li>
            </ul>
            <p>
              Le Prestataire s'engage à livrer à l'heure convenue. En cas
              d'imprévus (conditions météorologiques, trafic), le client sera
              prévenu dans les meilleurs délais.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">
              Article 5 — Annulation et modification
            </h2>
            <p>
              Le client peut annuler ou modifier sa commande tant qu'elle n'a
              pas été acceptée par un employé de Vite &amp; Gourmand. Une fois
              la commande passée en statut "acceptée", toute annulation ou
              modification devra faire l'objet d'un contact direct avec le
              Prestataire.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Article 6 — Matériel prêté</h2>
            <p>
              Dans le cadre de certaines prestations, du matériel peut être
              prêté au client (plats, récipients, équipements). Ce matériel doit
              être restitué dans un délai de
              <strong>10 jours ouvrés</strong> suivant la prestation.
            </p>
            <p>
              Passé ce délai, et sans restitution du matériel, le client sera
              redevable d'une somme forfaitaire de <strong>600€</strong> à titre
              de dédommagement. Le client sera notifié par email dès que ce
              statut est atteint.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">
              Article 7 — Allergènes et régimes
            </h2>
            <p>
              Les allergènes présents dans chaque plat sont indiqués sur la
              fiche de chaque menu. Il appartient au client de vérifier la
              composition des menus avant de passer commande. Vite &amp;
              Gourmand ne pourra être tenu responsable en cas d'omission de la
              part du client.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Article 8 — Réclamations</h2>
            <p>
              Toute réclamation doit être adressée dans les
              <strong>48 heures</strong> suivant la prestation par email à
              contact@viteetgourmand.com ou par téléphone au 05 56 87 42 13.
            </p>
          </div>

          <div class="legal-content">
            <h2 class="legal-content__title">Article 9 — Droit applicable</h2>
            <p>
              Les présentes CGV sont soumises au droit français. En cas de
              litige, les parties s'efforceront de trouver une solution amiable.
              À défaut, le tribunal compétent sera celui de Bordeaux.
            </p>
          </div>
        </div>
      </section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>
