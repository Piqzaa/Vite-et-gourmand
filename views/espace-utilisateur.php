<?php
ob_start();
?>
      <div class="dashboard">
        <aside class="dashboard__sidebar">
          <div class="dashboard__user">
            <div class="dashboard__avatar">
              <?= strtoupper(substr($user->getPrenom(), 0, 1) . substr($user->getNom(), 0, 1)) ?>
            </div>
            <div>
              <p class="dashboard__username"><?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?></p>
              <p class="dashboard__role">Utilisateur</p>
            </div>
          </div>
          <nav class="dashboard__nav" aria-label="Navigation espace utilisateur">
            <ul class="dashboard__nav-list">
              <li><a href="#commandes" class="dashboard__nav-link dashboard__nav-link--active">Mes commandes</a></li>
              <li><a href="#profil" class="dashboard__nav-link"> Mon profil </a></li>
            </ul>
          </nav>
        </aside>

        <div class="dashboard__content">
          <section class="dashboard__section" id="commandes">
            <div class="dashboard__section-header">
                <h1 class="dashboard__section-title">Mes commandes</h1>
            </div>

            <?php if (empty($commandes)): ?>
                <div class="empty-state">
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="index.php?page=menus" class="btn btn--primary">Découvrir la carte</a>
                </div>
            <?php else: ?>
                <?php foreach ($commandes as $cmd): ?>
                    <div class="commande-card <?= $cmd['statut'] === 'terminée' ? 'commande-card--done' : '' ?>">
                        <div class="commande-card__header">
                            <div>
                                <span class="commande-card__id">#CMD-<?= $cmd['commande_id'] ?></span>
                                <h2 class="commande-card__menu"><?= htmlspecialchars($cmd['menu_nom']) ?></h2>
                            </div>
                            <span class="commande-card__status <?= App\Helper\ViewHelper::getStatusClass($cmd['statut']) ?>">
                                <?= ucfirst($cmd['statut']) ?>
                            </span>
                        </div>

                        <div class="commande-card__infos">
                            <div class="commande-card__info">
                                <span class="commande-card__info-label">Date</span>
                                <span><?= date('d/m/Y à H:i', strtotime($cmd['date_commande'])) ?></span>
                            </div>
                            <div class="commande-card__info">
                                <span class="commande-card__info-label">Personnes</span>
                                <span><?= $cmd['nombre_personnes'] ?> pers.</span>
                            </div>
                            <div class="commande-card__info">
                                <span class="commande-card__info-label">Total</span>
                                <span class="commande-card__price"><?= number_format($cmd['prix_total_ttc'], 2) ?>€</span>
                            </div>
                        </div>

                        <?php if ($cmd['statut'] !== 'en attente' && $cmd['statut'] !== 'annulée'): ?>
                          <div class="commande-suivi">
                              <h4 class="commande-suivi__title">Suivi de ma commande</h4>
                              <ul class="commande-suivi__list">
                                  <?php foreach ($cmd['suivi_affichage'] as $step): ?>
                                      <li class="commande-suivi__step <?= $step['classe'] ?>">
                                          <span class="commande-suivi__dot"></span>
                                          <div class="commande-suivi__info">
                                              <strong><?= htmlspecialchars(ucfirst($step['nom'])) ?></strong>
                                              <?php if ($step['date']): ?>
                                                  <small><?= date('d/m/Y à H:i', strtotime($step['date'])) ?></small>
                                              <?php endif; ?>
                                              <?php if ($step['commentaire']): ?>
                                                  <p class="commande-suivi__comment"><?= htmlspecialchars($step['commentaire']) ?></p>
                                              <?php endif; ?>
                                          </div>
                                      </li>
                                  <?php endforeach; ?>
                              </ul>
                          </div>
                        <?php endif; ?>

                        <?php if ($cmd['statut'] === 'en attente'): ?>
                            <div class="commande-card__actions">
                                <form action="index.php?page=espace-utilisateur&action=cancel-commande" method="POST" onsubmit="return confirm('Annuler cette commande ?');">
                                    <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                                    <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                                    <button type="submit" class="btn btn--primary btn--sm">Annuler</button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if ($cmd['statut'] === 'terminée'): ?>
                            <?php 

                                if (!$cmd['has_avis']): 
                            ?>
                            <div class="avis-form">
                                <h3 class="avis-form__title">Votre avis nous intéresse</h3>
                                <form action="index.php?page=espace-utilisateur&action=create-avis" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
                                    <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                                    <div class="form-group">
                                        <label class="form-label">Note</label>
                                        <div class="avis-form__stars">
                                            <?php for($i=5; $i>=1; $i--): ?>
                                                <input type="radio" name="note" id="star<?= $cmd['commande_id'].$i ?>" value="<?= $i ?>" required />
                                                <label for="star<?= $cmd['commande_id'].$i ?>">★</label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <textarea name="commentaire" class="form-input" placeholder="Votre expérience..."></textarea>
                                    <div class="space-sm"></div>
                                    <button type="submit" class="btn btn--primary btn--sm">Envoyer</button>
                                </form>
                            </div>
                            <?php else: ?>
                                <p class="text-success"><span style="color: var(--success-color, #28a745);">&#10003;</span> Avis déjà envoyé. Merci !</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </section>

          <!-- MON PROFIL -->
          <section class="dashboard__section" id="profil">
            <div class="dashboard__section-header">
              <h1 class="dashboard__section-title">Mon profil</h1>
            </div>

            <form class="auth-form" action="index.php?page=espace-utilisateur&action=update-profil" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $securityService->generateCsrfToken() ?>">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="profil-prenom">Prénom</label>
                  <input
                    type="text"
                    id="profil-prenom"
                    name="prenom"
                    class="form-input"
                    value="<?= htmlspecialchars($user->getPrenom()) ?>"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label" for="profil-nom">Nom</label>
                  <input
                    type="text"
                    id="profil-nom"
                    name="nom"
                    class="form-input"
                    value="<?= htmlspecialchars($user->getNom()) ?>"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-email">Email</label>
                <input
                  type="email"
                  id="profil-email"
                  name="email"
                  class="form-input"
                  value="<?= htmlspecialchars($user->getEmail()) ?>"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-gsm">Téléphone</label>
                <input
                  type="tel"
                  id="profil-gsm"
                  name="gsm"
                  class="form-input"
                  value="<?= htmlspecialchars($user->getGsm()) ?>"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-adresse"
                  >Adresse postale</label
                >
                <input
                  type="text"
                  id="profil-adresse"
                  name="adresse"
                  class="form-input"
                  value="<?= htmlspecialchars($user->getAdressePostale()) ?>"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-ville"
                  >Ville</label
                >
                <input
                  type="text"
                  id="profil-ville"
                  name="ville"
                  class="form-input"
                  value="<?= htmlspecialchars($user->getVille()) ?>"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="profil-password">Nouveau mot de passe</label>
                <input type="password" id="profil-password" name="password" class="form-input"
                    placeholder="Laisser vide pour ne pas modifier" />
            </div>

            <div class="form-group" id="confirm-password-group" style="display:none">
                <label class="form-label" for="profil-password-confirm">Confirmer le mot de passe</label>
                <input type="password" id="profil-password-confirm" name="password_confirm" class="form-input"
                    placeholder="Répétez le mot de passe" />
            </div>

              <button type="submit" class="btn btn--primary">
                Enregistrer les modifications
              </button>
            </form>
          </section>
        </div>
      </div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout/main.php';
?>