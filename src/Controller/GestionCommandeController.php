<?php
namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Service\AuthService;
use App\Service\SecurityService;
use Exception;

class GestionCommandeController {
    public function __construct(
        private CommandeRepository $commandeRepo,
        private MenuRepository $menuRepo,
        private AuthService $authService,
        private SecurityService $securityService
    ) {}

    public function updateStatut(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-employe&error=csrf_invalid');
            exit;
        }

        $commandeId = (int)($_POST['commande_id'] ?? 0);
        $nouveauStatut = $_POST['statut'] ?? '';
        $commentaire = $_POST['commentaire'] ?? '';

        // Liste blanche des statuts autorisés
        $statutsAutorises = [
            'en attente',
            'accepté',
            'en préparation',
            'en cours de livraison',
            'livré',
            'en attente du retour de matériel',
            'terminée',
        ];

        if (!$commandeId || !in_array($nouveauStatut, $statutsAutorises, true)) {
            header('Location: index.php?page=espace-employe&error=champs_manquants');
            exit;
        }

        try {
            $this->commandeRepo->beginTransaction();
            $this->commandeRepo->updateStatut($commandeId, $nouveauStatut);
            $this->commandeRepo->addSuivi($commandeId, $nouveauStatut, $commentaire);
            $this->commandeRepo->commit();

            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=statut_mis_a_jour#commandes");
        } catch (Exception $e) {
            $this->commandeRepo->rollBack();
            header('Location: index.php?page=espace-employe&error=erreur_serveur');
        }
        exit;
    }

    public function annuler(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-employe&error=csrf_invalid');
            exit;
        }

        $commandeId = (int)($_POST['commande_id'] ?? 0);
        $motif = $_POST['motif'] ?? 'Annulée par le personnel';

        if (!$commandeId) {
            header('Location: index.php?page=espace-employe&error=commande_invalide');
            exit;
        }

        try {
            $this->commandeRepo->beginTransaction();
            
            $this->commandeRepo->updateStatut($commandeId, 'annulée');
            $this->commandeRepo->addSuivi($commandeId, 'annulée', $motif);
            
            $commande = $this->commandeRepo->findById($commandeId);
            if ($commande) {
                $this->menuRepo->incrementStock($commande->getMenuId());
            }

            $this->commandeRepo->commit();
            $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
            header("Location: index.php?page=$redirect&success=commande_annulee#commandes");
        } catch (Exception $e) {
            $this->commandeRepo->rollBack();
            header('Location: index.php?page=espace-employe&error=erreur_serveur');
        }
        exit;
    }
}
