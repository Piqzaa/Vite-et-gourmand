<?php
namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use App\Repository\PlatRepository;
use App\Repository\HoraireRepository;
use App\Service\AuthService;

class AdminController {
    public function __construct(
        private CommandeRepository $commandeRepo,
        private UserRepository $userRepo,
        private MenuRepository $menuRepo,
        private AvisRepository $avisRepo,
        private PlatRepository $platRepo,
        private HoraireRepository $horaireRepo,
        private AuthService $authService
    ) {}

    public function index(): void {
        if (!$this->authService->isAdmin()) {
            if ($this->authService->isEmploye()) {
                header('Location: index.php?page=espace-employe');
                exit;
            }
            header('Location: index.php?page=login');
            exit;
        }

        $commandesRaw = $this->commandeRepo->findAllWithDetails();
        $commandes = [];
        foreach ($commandesRaw as $cmd) {
            $commandes[] = [
                'commande_id' => $cmd->getId(),
                'menu_nom' => $cmd->getMenuNom(),
                'client_nom' => $cmd->getClientNom(),
                'client_prenom' => $cmd->getClientPrenom(),
                'client_email' => $cmd->getClientEmail(),
                'client_gsm' => $cmd->getClientGsm(),
                'statut' => $cmd->getStatut(),
                'date_prestation' => $cmd->getDatePrestation()->format('Y-m-d'),
                'heure_prestation' => $cmd->getHeurePrestation(),
                'adresse_livraison' => $cmd->getAdresseLivraison(),
                'prix_total_ttc' => $cmd->getPrixTotalTtc(),
                'nombre_personnes' => $cmd->getNombrePersonnes()
            ];
        }
        $employes = $this->userRepo->findByRole('employe');
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $statsCA = $this->commandeRepo->getGlobalStats();
        $statsParMenu = $this->commandeRepo->getStatsByMenu();
        $horaires = $this->horaireRepo->findAll();
        $securityService = new \App\Service\SecurityService();

        $title = 'Espace de gestion';
        $description = 'Gérez les commandes, les employés, les menus et les avis clients.';
        require __DIR__ . '/../../views/espace-admin.php';
    }
}
