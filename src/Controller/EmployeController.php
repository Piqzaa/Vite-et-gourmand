<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use App\Repository\PlatRepository;
use App\Repository\HoraireRepository;
use App\Service\AuthService;

class EmployeController {
    public function __construct(
        private CommandeRepository $commandeRepo,
        private MenuRepository $menuRepo,
        private AvisRepository $avisRepo,
        private PlatRepository $platRepo,
        private HoraireRepository $horaireRepo,
        private AuthService $authService
    ) {}

    public function index(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        $commandes = $this->commandeRepo->findAllWithDetails();
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $horaires = $this->horaireRepo->findAll();

        $title = 'Espace employé';
        $description = 'Gérez les commandes, consultez les avis clients et mettez à jour les horaires d\'ouverture.';

        require __DIR__ . '/../../views/espace-employe.php';
    }
}
