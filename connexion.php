<!doctype html>
<html lang="fr">

<?php 
$title = 'Connexion ';
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
            action="assets/php/auth/login.php"
            method="POST"
            
          >
            <?php if (isset($_GET['error'])): ?>
              <p class="form__error">
                  <?php
                  $errors = [
                      'champs_vides'           => 'Veuillez remplir tous les champs.',
                      'identifiants_invalides' => 'Email ou mot de passe incorrect.',
                  ];
                  echo $errors[$_GET['error']] ?? 'Une erreur est survenue.';
                  ?>
              </p>
            <?php endif; ?>
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
                <a href="reset-password.php" class="form-link"
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
            <a href="inscription.php" class="form-link">Créer un compte</a>
          </p>
        </div>
      </section>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>