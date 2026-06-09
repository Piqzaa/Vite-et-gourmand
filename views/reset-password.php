<?php
ob_start();
?>
    <section class="auth-page">
        <div class="auth-card">
            <h1 class="auth-card__title">Nouveau mot de passe</h1>
            <form action="index.php?page=reset-password&action=process" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <?php if (isset($_GET['error'])): ?>
                    <p class="form__error">
                        <?php
                        $errors = [
                            'too_short'     => 'Le mot de passe doit faire au moins 10 caractères.',
                            'not_complex'   => 'Le mot de passe doit contenir une majuscule, un chiffre et un caractère spécial.',
                            'mismatch'      => 'Les mots de passe ne correspondent pas.',
                            'db_error'      => 'Une erreur technique est survenue. Réessayez.',
                            'token_invalide' => 'Session expirée ou invalide.'
                        ];
                        echo $errors[$_GET['error']] ?? 'Une erreur est survenue.';
                        ?>
                    </p>
                <?php endif; ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-group">
                    <label class="form-label" for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="10 car. min, 1 maj, 1 min, 1 chiffre, 1 spécial" required />
                </div>

                <div class="form-group">
                    <label class="form-label" for="password-confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password-confirm" name="password_confirm" 
                           class="form-input" required />
                </div>

                <button type="submit" class="btn btn--primary btn--full">
                    Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout/main.php';
?>
