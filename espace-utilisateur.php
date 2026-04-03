<?php
require_once __DIR__ . '/assets/php/includes/functions.php';
require_once __DIR__ . '/assets/php/config/db.php';
require_once __DIR__ . '/assets/php/includes/session.php';

sessionStart();
$pdo = getDB();

if (!isConnected() || getUserRole() !== 'utilisateur') {
    header('Location: connexion.php');
    exit();
}

$userId = getUserId();

// 1. Récupération utilisateur
$queryUser = "SELECT nom, prenom, email, gsm, adresse_postale, ville FROM utilisateur WHERE utilisateur_id = :id";
$stmtUser = $pdo->prepare($queryUser);
$stmtUser->execute(['id' => $userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// 2. Définition du workflow de suivi (ordre des statuts)
// Attention : "annulée" n'est pas dedans car c'est un statut "hors circuit" classique, on le gère à part.
$workflow = ['en attente', 'accepté', 'en préparation', 'en cours de livraison', 'en attente du retour de matériel', 'livré', 'terminée'];

// 3. Récupération des commandes avec leur suivi
$queryCommandes = "
    SELECT c.*, m.titre AS menu_nom 
    FROM commande c 
    JOIN menu m ON c.menu_id = m.menu_id 
    WHERE c.utilisateur_id = :id 
    ORDER BY c.date_commande DESC";

$stmtCommandes = $pdo->prepare($queryCommandes);
$stmtCommandes->execute(['id' => $userId]);
$commandesRaw = $stmtCommandes->fetchAll(PDO::FETCH_ASSOC);

$commandes = [];
foreach ($commandesRaw as $cmd) {
    $querySuivi = "SELECT * FROM suivi_commande WHERE commande_id = :cid ORDER BY date_modif ASC";
    $stmtSuivi = $pdo->prepare($querySuivi);
    $stmtSuivi->execute(['cid' => $cmd['commande_id']]);
    $historiqueDB = $stmtSuivi->fetchAll(PDO::FETCH_ASSOC);

    $suiviIndexed = [];
    foreach ($historiqueDB as $h) {
        $suiviIndexed[$h['statut']] = $h;
    }

    $etapesVue = [];
    $foundActive = false;
    $nextDisplayed = false;
    
    // On vérifie si la commande a atteint un statut final
    $isFinalStatus = ($cmd['statut'] === 'terminée' || $cmd['statut'] === 'annulée');

    foreach ($workflow as $index => $statusName) {
        $isDone = isset($suiviIndexed[$statusName]);
        $isActive = ($cmd['statut'] === $statusName);
        
        $classe = "";
        
        if ($isActive) {
            // Si c'est l'étape actuelle ET que la commande est terminée, elle passe en --done. Sinon --active.
            $classe = $isFinalStatus ? "commande-suivi__step--done" : "commande-suivi__step--active";
            $foundActive = true;
        } elseif ($isDone && !$foundActive) { 
            // C'est une étape passée (déjà dans l'historique ET on n'a pas encore atteint l'active)
            $classe = "commande-suivi__step--done";
        } elseif ($foundActive && !$nextDisplayed && !$isFinalStatus) {
            // On affiche l'étape suivante avec une classe vide (point gris) uniquement si la commande n'est pas finalisée
            $classe = ""; 
            $nextDisplayed = true;
        } else {
            // Les étapes trop lointaines ne sont pas affichées
            continue; 
        }

        $etapesVue[] = [
            'nom' => $statusName,
            'classe' => $classe,
            'date' => $isDone ? $suiviIndexed[$statusName]['date_modif'] : null,
            'commentaire' => $isDone ? $suiviIndexed[$statusName]['commentaire'] : null
        ];
    }

    // Gestion spécifique du cas "annulée" car il n'est pas dans le tableau $workflow
    if ($cmd['statut'] === 'annulée') {
        $etapesVue[] = [
            'nom' => 'annulée',
            'classe' => 'commande-suivi__step--done', // Point plein car c'est un statut final
            'date' => isset($suiviIndexed['annulée']) ? $suiviIndexed['annulée']['date_modif'] : null,
            'commentaire' => isset($suiviIndexed['annulée']) ? $suiviIndexed['annulée']['commentaire'] : null
        ];
    }

    $cmd['suivi_affichage'] = $etapesVue;
    $commandes[] = $cmd;
}

$title = 'Espace Utilisateur';
$description = 'Gérez vos commandes, modifiez votre profil et laissez votre avis sur Vite & Gourmand.';

ob_start();
?>
      <div class="dashboard">
        <aside class="dashboard__sidebar">
          <div class="dashboard__user">
            <div class="dashboard__avatar">
              <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
            </div>
            <div>
              <p class="dashboard__username"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
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
                    <a href="carte.php" class="btn btn--primary">Découvrir la carte</a>
                </div>
            <?php else: ?>
                <?php foreach ($commandes as $cmd): ?>
                    <div class="commande-card <?= $cmd['statut'] === 'terminée' ? 'commande-card--done' : '' ?>">
                        <div class="commande-card__header">
                            <div>
                                <span class="commande-card__id">#CMD-<?= $cmd['commande_id'] ?></span>
                                <h2 class="commande-card__menu"><?= htmlspecialchars($cmd['menu_nom']) ?></h2>
                            </div>
                            <span class="commande-card__status <?= getStatusClass($cmd['statut']) ?>">
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
                                <a href="modifier-commande.php?id=<?= $cmd['commande_id'] ?>" class="btn btn--secondary btn--sm">Modifier</a>
                                <form action="assets/php/user/cancel-commande.php" method="POST" onsubmit="return confirm('Annuler cette commande ?');">
                                    <input type="hidden" name="commande_id" value="<?= $cmd['commande_id'] ?>">
                                    <button type="submit" class="btn btn--primary btn--sm">Annuler</button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if ($cmd['statut'] === 'terminée'): ?>
                            <?php 
                                $checkAvis = $pdo->prepare("SELECT avis_id FROM avis WHERE commande_id = ?");
                                $checkAvis->execute([$cmd['commande_id']]);
                                if (!$checkAvis->fetch()): 
                            ?>
                            <div class="avis-form">
                                <h3 class="avis-form__title">Votre avis nous intéresse</h3>
                                <form action="assets/php/avis/create.php" method="POST">
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
                                <p class="text-success"><i class="fas fa-check"></i> Avis déjà envoyé. Merci !</p>
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

            <form class="auth-form" action="assets/php/user/update-profil.php" method="POST">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="profil-prenom">Prénom</label>
                  <input
                    type="text"
                    id="profil-prenom"
                    name="prenom"
                    class="form-input"
                    value="<?= htmlspecialchars($user['prenom']) ?>"
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
                    value="<?= htmlspecialchars($user['nom']) ?>"
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
                  value="<?= htmlspecialchars($user['email']) ?>"
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
                  value="<?= htmlspecialchars($user['gsm']) ?>"
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
                  value="<?= htmlspecialchars($user['adresse_postale'] ?? '') ?>"
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
                  value="<?= htmlspecialchars($user['ville'] ?? '') ?>"
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
require_once 'includes/layout.php';
?>