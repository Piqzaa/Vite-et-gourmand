<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Service\AuthService;
use App\Service\LoggerService;

class UserController
{
  public function __construct(
    private UserRepository $userRepository,
    private CommandeRepository $commandeRepository,
    private AuthService $authService,
    private LoggerService $logger
  ) {}

  public function index(): void
  {
    if (!$this->authService->isConnected()) {
      header('Location: index.php?page=login');
      exit;
    }

    $userId = $_SESSION['user_id'];
    $user = $this->userRepository->findById($userId);
    $commandesRaw = $this->commandeRepository->findByUserId($userId);
    
    $commandes = [];
    foreach ($commandesRaw as $cmd) {
    $cmd['suivi_affichage'] = $this->calculateSuivi($cmd);
    $cmd['has_avis'] = $this->commandeRepository->hasAvis($cmd['commande_id']);
    $commandes[] = $cmd;
    }

    $title = 'Mon compte';

    require __DIR__ . '/../../views/espace-utilisateur.php';
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
