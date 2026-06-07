<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Service\AuthService;
use App\Service\LoggerService;

class UserController
{
  public function __construct(
    private UserRepository $userRepository,
    private CommandeRepository $commandeRepository,
    private AvisRepository $avisRepository,
    private AuthService $authService,
    private LoggerService $logger
  ) {}

  public function index(): void
  {
    if (!$this->authService->isConnected()) {
      header('Location: index.php?page=login');
      exit;
    }

    if (!$this->authService->isUser()) {
        $redirect = 'index.php?page=home';
        if ($this->authService->isAdmin()) $redirect = 'index.php?page=espace-admin';
        elseif ($this->authService->isEmploye()) $redirect = 'index.php?page=espace-employe';
        header('Location: ' . $redirect);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $user = $this->userRepository->findById($userId);
    $commandesRaw = $this->commandeRepository->findByUserId($userId);
    
    $commandes = [];
    foreach ($commandesRaw as $cmd) {
        $cmd['suivi_affichage'] = $this->calculateSuivi($cmd);
        $cmd['has_avis'] = $this->avisRepository->existsForCommande($cmd['commande_id']);
        $commandes[] = $cmd;
    }

    $title = 'Mon compte';
    $description = 'Gérez votre profil et suivez vos commandes Vite & Gourmand.';

    require __DIR__ . '/../../views/espace-utilisateur.php';
  }

  /**
   * Créer un avis
   */
  public function createAvis(): void {
    if (!$this->authService->isUser()) {
        header('Location: index.php?page=login');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $commandeId = (int)($_POST['commande_id'] ?? 0);
    $note = (int)($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    $commande = $this->commandeRepository->findById($commandeId);
    if (!$commande || $commande['utilisateur_id'] !== $userId || $commande['statut'] !== 'terminée') {
        header('Location: index.php?page=espace-utilisateur&error=avis_non_autorise#commandes');
        exit;
    }

    if ($this->avisRepository->existsForCommande($commandeId)) {
        header('Location: index.php?page=espace-utilisateur&error=avis_existant#commandes');
        exit;
    }

    if ($this->avisRepository->save([
        'note' => $note,
        'commentaire' => $commentaire,
        'utilisateur_id' => $userId,
        'commande_id' => $commandeId
    ])) {
        header('Location: index.php?page=espace-utilisateur&success=avis_envoye#commandes');
    } else {
        header('Location: index.php?page=espace-utilisateur&error=save_failed#commandes');
    }
    exit;
  }

  private function calculateSuivi(array $cmd): array {
    $workflow = ['en attente', 'accepté', 'en préparation', 'en cours de livraison', 'en attente du retour de matériel', 'livré', 'terminée'];
    $historiqueDB = $this->commandeRepository->getSuiviByCommandeId($cmd['commande_id']);

    $suiviIndexed = [];
    foreach ($historiqueDB as $h) {
        $suiviIndexed[$h['statut']] = $h;
    }

    $etapesVue = [];
    $foundActive = false;
    $nextDisplayed = false;
    $isFinalStatus = ($cmd['statut'] === 'terminée' || $cmd['statut'] === 'annulée');

    foreach ($workflow as $statusName) {
        $isDone = isset($suiviIndexed[$statusName]);
        $isActive = ($cmd['statut'] === $statusName);
        
        $classe = "";
        if ($isActive) {
            $classe = $isFinalStatus ? "commande-suivi__step--done" : "commande-suivi__step--active";
            $foundActive = true;
        } elseif ($isDone && !$foundActive) { 
            $classe = "commande-suivi__step--done";
        } elseif ($foundActive && !$nextDisplayed && !$isFinalStatus) {
            $classe = ""; 
            $nextDisplayed = true;
        } else {
            continue; 
        }

        $etapesVue[] = [
            'nom' => $statusName,
            'classe' => $classe,
            'date' => $isDone ? $suiviIndexed[$statusName]['date_modif'] : null,
            'commentaire' => $isDone ? $suiviIndexed[$statusName]['commentaire'] : null
        ];
    }

    if ($cmd['statut'] === 'annulée') {
        $etapesVue[] = [
            'nom' => 'annulée',
            'classe' => 'commande-suivi__step--done',
            'date' => isset($suiviIndexed['annulée']) ? $suiviIndexed['annulée']['date_modif'] : null,
            'commentaire' => isset($suiviIndexed['annulée']) ? $suiviIndexed['annulée']['commentaire'] : null
        ];
    }
    return $etapesVue;
  }
}
