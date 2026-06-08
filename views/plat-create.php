<?php
ob_start();
$errorMessages = [
    'champs_manquants' => 'Veuillez remplir tous les champs obligatoires.',
    'image_manquante' => 'Aucune image n\'a été fournie.',
    'format_invalide' => 'Format d\'image non autorisé. Utilisez JPG, PNG ou WEBP.',
    'fichier_trop_lourd' => 'L\'image est trop volumineuse (max 2 Mo).',
    'upload_echoue' => 'Erreur lors de l\'upload de l\'image.',
    'erreur_serveur' => 'Une erreur est survenue. Veuillez réessayer.'
];
$error = $_GET['error'] ?? '';
?>
    <section class="page-header">
        <div class="page-header__container">
            <span class="section-eyebrow">Administration</span>
            <h1 class="page-header__title">Créer un plat</h1>
        </div>
    </section>

    <section class="commande-page">
        <div class="commande-page__container">
            <div class="commande-form-wrapper">
                <?php if ($error && isset($errorMessages[$error])): ?>
                    <p class="form__error" style="color: var(--error-color, red); margin-bottom: 1rem;">
                        <?= htmlspecialchars($errorMessages[$error]) ?>
                    </p>
                <?php endif; ?>
                <form action="index.php?page=plat-create&action=process" method="POST" class="auth-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="form-group">
                        <label class="form-label" for="libelle">Nom du plat</label>
                        <input type="text" id="libelle" name="libelle" class="form-input" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="type">Type</label>
                        <select id="type" name="type" class="filters__select" required>
                            <option value="">Choisir un type</option>
                            <option value="entrée">Entrée</option>
                            <option value="plat">Plat</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Allergènes</label>
                        <div class="plats-checkboxes">
                            <?php foreach ($allergenes as $a): ?>
                            <label class="plat-checkbox">
                                <input type="checkbox" name="allergenes[]" value="<?= $a['allergene_id'] ?>" />
                                <?= htmlspecialchars($a['libelle']) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                        <div class="form-group">
                            <label class="form-label" for="plat-image">Image du plat</label>
                            <input type="file" id="plat-image" name="image" class="form-input" accept="image/jpeg,image/jpg,image/png,image/webp" required />
                            <p class="form-hint">Format : JPG, PNG, WEBP. Max 2 Mo.</p>
                        </div>
                    <div class="space-md"></div>
                    <div class="commande-nav">
                        <a href="index.php?page=espace-admin#menus" class="btn btn--secondary">← Annuler</a>
                        <button type="submit" class="btn btn--primary">Créer le plat</button>
                    </div>

                </form>
            </div>
        </div>
    </section>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout/main.php';
?>
