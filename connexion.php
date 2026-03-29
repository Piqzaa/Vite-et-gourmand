<!doctype html>
<html lang="fr">

<?php 
$title = 'Détails du menu ';
require __DIR__ . '/includes/head.php'; ?>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content">
      <section class="auth-page">
        <div class="auth-card">
          <div class="auth-card__header">
            <h1 class="auth-card__title">Connexion</h1>
            <p class="auth-card__sub">Accédez à votre espace personnel.</p>
          </div>

          <form
            class="auth-form"
            action="php/auth/login.php"
            method="POST"
            novalidate
          >
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
              <div class="form-group__row">
                <label class="form-label" for="password">Mot de passe</label>
                <a href="reset-password.html" class="form-link"
                  >Mot de passe oublié ?</a
                >
              </div>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="••••••••••"
                required
                autocomplete="current-password"
              />
            </div>

            <button type="submit" class="btn btn--primary btn--full">
              Se connecter
            </button>
          </form>

          <p class="auth-card__footer">
            Pas encore de compte ?
            <a href="inscription.html" class="form-link">Créer un compte</a>
          </p>
        </div>
      </section>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>