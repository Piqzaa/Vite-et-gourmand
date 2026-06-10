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

        if ($this->authService->isAdmin()) {
            header('Location: index.php?page=espace-admin');
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
        $menus = $this->menuRepo->findAll();
        $avis = $this->avisRepo->findPending();
        $platsAvecAllergenes = $this->platRepo->findAllWithAllergenes();
        $horaires = $this->horaireRepo->findAll();
        $securityService = new \App\Service\SecurityService();

        $title = 'Espace employé';
        $description = 'Gérez les commandes, consultez les avis clients et mettez à jour les horaires d\'ouverture.';

        require __DIR__ . '/../../views/espace-employe.php';
    }
}
