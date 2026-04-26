<?php
session_start();
$title = 'Contact';
$description = 'Contactez Vite & Gourmand pour toute question ou demande particulière concernant nos menus traiteur à Bordeaux.';
ob_start();
?>
      <section class="page-header">
        <div class="page-header__container">
          <span class="section-eyebrow">On vous répond rapidement</span>
          <h1 class="page-header__title">Contactez-nous</h1>
          <p class="page-header__sub">Une question sur nos menus ? Écrivez-nous.</p>
        </div>
      </section>

      <section class="contact-page">
        <div class="contact-page__container">
          <div class="contact-form-wrapper">
            
            <?php if(isset($_SESSION['contact_success'])): ?>
              <div class="alert alert--success" role="status"><?= $_SESSION['contact_success']; unset($_SESSION['contact_success']); ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['contact_error'])): ?>
              <div class="alert alert--error" role="alert"><?= $_SESSION['contact_error']; unset($_SESSION['contact_error']); ?></div>
            <?php endif; ?>

            <form class="auth-form" action="assets/php/contact/send.php" method="POST" novalidate>
              <div class="form-group">
                <label class="form-label" for="contact-email">Votre adresse email (requis)</label>
                <input
                  type="email"
                  id="contact-email"
                  name="email"
                  class="form-input"
                  placeholder="votre@email.com"
                  required
                  aria-required="true"
                  autocomplete="email"
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="contact-titre">Titre de votre message (requis)</label>
                <input
                  type="text"
                  id="contact-titre"
                  name="titre"
                  class="form-input"
                  required
                  aria-required="true"
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="contact-message">Votre message (requis)</label>
                <textarea
                  id="contact-message"
                  name="message"
                  class="form-input form-textarea"
                  required
                  aria-required="true"
                  rows="6"
                ></textarea>
              </div>

              <button type="submit" class="btn btn--primary btn--full">
                Envoyer le message
              </button>
            </form>
          </div>

          <div class="contact-infos">
            <address class="contact-info-block">
              <h2 class="contact-info-block__title">Vite &amp; Gourmand</h2>
              <p class="contact-info-block__text">27 Rue des Faures<br />33000 Bordeaux</p>
            </address>
            </div>
        </div>
      </section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>