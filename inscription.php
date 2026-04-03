<?php
$title = 'Inscription';
$description = 'Rejoignez Vite & Gourmand pour commander vos menus. Créez votre compte en quelques étapes simples et profitez de nos délicieux plats traiteur à Bordeaux.';
ob_start();
?>
      <section class="auth-page">
        <div class="auth-card auth-card--wide">
          <div class="auth-card__header">
            <h1 class="auth-card__title">Créer un compte</h1>
            <p class="auth-card__sub">
              Rejoignez Vite & Gourmand pour commander vos menus.
            </p>
          </div>

          <form
            class="auth-form"
            action="assets/php/auth/register.php"
            method="POST"
            
          >
          <?php if (!empty($_SESSION['register_errors'])): ?>
            <div class="form-errors">
              <ul>
                <?php foreach ($_SESSION['register_errors'] as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php unset($_SESSION['register_errors']); ?>
          <?php endif; ?>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="prenom">Prénom</label>
                <input
                  type="text"
                  id="prenom"
                  name="prenom"
                  class="form-input"
                  placeholder="Votre prénom"
                  value="<?= htmlspecialchars($_SESSION['register_old']['prenom'] ?? '') ?>"
                  required
                  autocomplete="given-name"
                />
              </div>
              <div class="form-group">
                <label class="form-label" for="nom">Nom</label>
                <input
                  type="text"
                  id="nom"
                  name="nom"
                  class="form-input"
                  placeholder="Votre nom"
                  value="<?= htmlspecialchars($_SESSION['register_old']['nom'] ?? '') ?>"
                  required
                  autocomplete="family-name"
                />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="email">Adresse email</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                placeholder="votre@email.com"
                value="<?= htmlspecialchars($_SESSION['register_old']['email'] ?? '') ?>"
                required
                autocomplete="email"
              />
            </div>

            <div class="form-group">
              <label class="form-label" for="gsm">Numéro de téléphone</label>
              <input
                type="tel"
                id="gsm"
                name="gsm"
                class="form-input"
                placeholder="Votre numéro de téléphone"
                value="<?= htmlspecialchars($_SESSION['register_old']['gsm'] ?? '') ?>"
                required
                autocomplete="tel"
              />
            </div>

            <div class="form-group">
              <label class="form-label" for="adresse">Adresse postale</label>
              <input
                type="text"
                id="adresse"
                name="adresse"
                class="form-input"
                placeholder="Votre adresse"
                value="<?= htmlspecialchars($_SESSION['register_old']['adresse'] ?? '') ?>"
                required
                autocomplete="street-address"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="ville">Ville</label>
                <input
                  type="text"
                  id="ville"
                  name="ville"
                  class="form-input"
                  placeholder="Votre ville"
                  value="<?= htmlspecialchars($_SESSION['register_old']['ville'] ?? '') ?>"
                  required
                  autocomplete="address-level2"
                />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="password">Mot de passe</label>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="••••••••••"
                required
                autocomplete="new-password"
              />
              <p class="form-hint">
                10 caractères minimum — 1 majuscule, 1 minuscule, 1 chiffre, 1
                caractère spécial.
              </p>
            </div>

            <div class="form-group">
              <label class="form-label" for="password-confirm"
                >Confirmer le mot de passe</label
              >
              <input
                type="password"
                id="password-confirm"
                name="password_confirm"
                class="form-input"
                placeholder="••••••••••"
                required
                autocomplete="new-password"
              />
            </div>

            <div class="form-group">
              <label class="form-checkbox">
                <input type="checkbox" name="cgv" required />
                <span
                  >J'accepte les
                  <a href="cgv.php" class="form-link"
                    >conditions générales de vente</a
                  >
                  et la
                  <a href="mentions-legales.php" class="form-link"
                    >politique de confidentialité</a
                  >.</span
                >
              </label>
            </div>

            <button type="submit" class="btn btn--primary btn--full">
              Créer mon compte
            </button>
            <?php unset($_SESSION['register_old']); ?>
          </form>

          <p class="auth-card__footer">
            Déjà un compte ?
            <a href="connexion.php" class="form-link">Se connecter</a>
          </p>
        </div>
      </section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>