<?php
ob_start();
?>
      <section class="page-header">
        <div class="page-header__container">
          <h1 class="page-header__title">Erreur 404 — Page non trouvée</h1>
        </div>
      </section>

      <section class="legal-page">
        <div class="legal-page__container">
          <div class="legal-content">
            <h2 class="legal-content__title">Oups, cette page n'existe pas</h2>
            <p>
              La page que vous recherchez n'existe pas ou a été déplacée.
              Vous pouvez revenir à l'accueil pour continuer votre navigation.
            </p>
            <p>
              <a class="btn btn--primary" href="index.php?page=home">Retour à l'accueil</a>
            </p>
          </div>
        </div>
      </section>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout/main.php';
?>