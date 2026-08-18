<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use App\Repository\PlatRepository;
use App\Repository\HoraireRepository;
use App\Service\AuthService;
use App\Service\SecurityService;
use App\Helper\ViewHelper;

class EmployeController {
    public function __construct(
        private CommandeRepository $commandeRepo,
        private MenuRepository $menuRepo,
        private AvisRepository $avisRepo,
        private PlatRepository $platRepo,
        private HoraireRepository $horaireRepo,
        private AuthService $authService,
        private SecurityService $securityService
    ) {}

    public function index(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($this->authService->isAdmin()) {
            header('Location: index.php?page=espace-admin');
            exit;
        }

        $commandesRaw = $this->commandeRepo->findAllWithDetails();
        $commandes = ViewHelper::mapCommandes($commandesRaw);
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $horaires = $this->horaireRepo->findAll();
        $securityService = $this->securityService;

        $title = 'Espace employé';
        $description = 'Gérez les commandes, consultez les avis clients et mettez à jour les horaires d\'ouverture.';

        require __DIR__ . '/../../views/espace-employe.php';
    }
}
