<?php
$title = 'Connexion';
$description = 'Accédez à votre espace personnel sur Vite & Gourmand. Connectez-vous pour gérer vos commandes, consulter votre historique d\'achats et profiter de nos délicieux menus traiteur à Bordeaux.';
ob_start();
?>
  <!-- AUTH PAGE -->
<section class="auth-page">
    <div class="auth-card">
        
        <div id="login-view">
            <div class="auth-card__header">
                <h1 class="auth-card__title">Connexion</h1>
                <p class="auth-card__sub">Accédez à votre espace personnel.</p>
            </div>

            <form class="auth-form" action="assets/php/auth/login.php" method="POST">
                <?php if (isset($_GET['error'])): ?>
                    <p class="form__error">
                        <?php
                        $errors = ['champs_vides' => 'Veuillez remplir tous les champs.', 'identifiants_invalides' => 'Email ou mot de passe incorrect.'];
                        echo $errors[$_GET['error']] ?? 'Une erreur est survenue.';
                        ?>
                    </p>
                <?php endif; ?>
                <div class="space-sm"></div>
                <div class="form-group">
                    <label class="form-label" for="email">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required autocomplete="email" />
                </div>

                <div class="form-group">
                    <div class="form-group__row">
                        <label class="form-label" for="password">Mot de passe</label>
                        <a href="#" id="btn-show-forgot" class="form-link">Mot de passe oublié ?</a>
                    </div>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••••" required autocomplete="current-password" />
                </div>

                <button type="submit" class="btn btn--primary btn--full">Se connecter</button>
            </form>
        </div>

        <div id="forgot-view" style="display: none;">
            <div class="auth-card__header">
                <h2 class="auth-card__title">Mot de passe oublié ?</h2>
                <p class="auth-card__sub">Saisissez votre email pour recevoir un lien.</p>
            </div>
            <div class="space-sm"></div>

            <form action="assets/php/auth/forgot-password.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label class="form-label" for="forgot-email">Votre email</label>
                    <input type="email" id="forgot-email" name="email" class="form-input" placeholder="votre@email.com" required />
                </div>
                <button type="submit" class="btn btn--secondary btn--full">Recevoir le lien</button>
                
                    <a href="#" id="btn-show-login" class="form-link"> Retour à la connexion</a>

            </form>
        </div>

        <p class="auth-card__footer">
            Pas encore de compte ?
            <a href="inscription.php" class="form-link">Créer un compte</a>
        </p>
    </div>
</section>
<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?>