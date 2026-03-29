<!doctype html>
<html lang="fr">

<?php 
$title = 'Détails du menu ';
require __DIR__ . '/includes/head.php'; ?>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content">
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
            action="php/auth/register.php"
            method="POST"
            novalidate
          >
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="prenom">Prénom</label>
                <input
                  type="text"
                  id="prenom"
                  name="prenom"
                  class="form-input"
                  placeholder="Julie"
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
                  placeholder="Dupont"
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
                placeholder="06 12 34 56 78"
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
                placeholder="12 rue des Lilas"
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
                  placeholder="Bordeaux"
                  required
                  autocomplete="address-level2"
                />
              </div>
              <div class="form-group">
                <label class="form-label" for="pays">Pays</label>
                <input
                  type="text"
                  id="pays"
                  name="pays"
                  class="form-input"
                  placeholder="France"
                  required
                  autocomplete="country-name"
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
                  <a href="cgv.html" class="form-link"
                    >conditions générales de vente</a
                  >
                  et la
                  <a href="mentions-legales.html" class="form-link"
                    >politique de confidentialité</a
                  >.</span
                >
              </label>
            </div>

            <button type="submit" class="btn btn--primary btn--full">
              Créer mon compte
            </button>
          </form>

          <p class="auth-card__footer">
            Déjà un compte ?
            <a href="connexion.html" class="form-link">Se connecter</a>
          </p>
        </div>
      </section>
    </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>