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
            header('Location: index.php?page=login');
            exit;
        }

        $commandes = $this->commandeRepo->findAllWithDetails();
        $employes = $this->userRepo->findByRole('employe');
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $statsCA = $this->commandeRepo->getGlobalStats();
        $statsParMenu = $this->commandeRepo->getStatsByMenu();
        $horaires = $this->horaireRepo->findAll();

        $title = 'Espace administrateur';
        $description = 'Gérez les commandes, les employés, les menus et les avis clients depuis votre espace administrateur.';
        require __DIR__ . '/../../views/espace-admin.php';
    }
}