<?php
$title = 'Contact';
$description = 'Contactez Vite & Gourmand pour toute question ou demande particulière concernant nos menus traiteur à Bordeaux. Nous sommes là pour vous aider et répondre à vos besoins.';
ob_start();
?>
      <section class="page-header">
        <div class="page-header__container">
          <span class="section-eyebrow">On vous répond rapidement</span>
          <h1 class="page-header__title">Contactez-nous</h1>
          <p class="page-header__sub">
            Une question sur nos menus ou une demande particulière ?
            Écrivez-nous.
          </p>
        </div>
      </section>

      <section class="contact-page">
        <div class="contact-page__container">
          <!-- FORMULAIRE -->
          <div class="contact-form-wrapper">
            <form
              class="auth-form"
              action="php/contact/send.php"
              method="POST"
              novalidate
            >
              <div class="form-group">
                <label class="form-label" for="contact-email"
                  >Votre adresse email</label
                >
                <input
                  type="email"
                  id="contact-email"
                  name="email"
                  class="form-input"
                  placeholder="votre@email.com"
                  required
                  autocomplete="email"
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="contact-titre"
                  >Titre de votre message</label
                >
                <input
                  type="text"
                  id="contact-titre"
                  name="titre"
                  class="form-input"
                  placeholder="Ex : Question sur le Menu des Fêtes"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="contact-message"
                  >Votre message</label
                >
                <textarea
                  id="contact-message"
                  name="message"
                  class="form-input form-textarea"
                  placeholder="Décrivez votre demande..."
                  required
                  rows="6"
                ></textarea>
              </div>

              <button type="submit" class="btn btn--primary btn--full">
                Envoyer le message
              </button>
            </form>
          </div>

          <!-- INFOS CONTACT -->
          <div class="contact-infos">
            <div class="contact-info-block">
              <h2 class="contact-info-block__title">Vite &amp; Gourmand</h2>
              <p class="contact-info-block__text">
                27 Rue des Faures<br />33000 Bordeaux
              </p>
            </div>

            <div class="contact-info-block">
              <h3 class="contact-info-block__label">Téléphone</h3>
              <p class="contact-info-block__text">05 56 87 42 13</p>
            </div>

            <div class="contact-info-block">
              <h3 class="contact-info-block__label">Email</h3>
              <p class="contact-info-block__text">contact@viteetgourmand.com</p>
            </div>

            <div class="contact-info-block">
              <h3 class="contact-info-block__label">Horaires</h3>
              <ul class="contact-hours">
                <li><span>Lundi – Vendredi</span><span>9h00 – 18h00</span></li>
                <li><span>Samedi</span><span>10h00 – 16h00</span></li>
                <li><span>Dimanche</span><span>10h00 – 13h00</span></li>
              </ul>
            </div>
          </div>
        </div>
      </section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>