<?php
namespace App\Controller;

use App\Repository\AvisRepository;
use App\Service\AuthService;
use App\Service\SecurityService;

class AvisModerationController {
    public function __construct(
        private AvisRepository $avisRepo,
        private AuthService $authService,
        private SecurityService $securityService
    ) {}

    public function moderer(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-employe&error=csrf_invalid#avis');
            exit;
        }

        $id = (int)($_POST['avis_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($action === 'valider') {
            $this->avisRepo->updateStatut($id, true);
        } else {
            $this->avisRepo->delete($id);
        }

        $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
        header("Location: index.php?page=$redirect&success=avis_modere#avis");
        exit;
    }
}
