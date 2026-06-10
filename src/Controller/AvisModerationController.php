<?php
namespace App\Controller;

use App\Repository\AvisRepository;
use App\Service\AuthService;

class AvisModerationController {
    public function __construct(
        private AvisRepository $avisRepo,
        private AuthService $authService
    ) {}

    public function moderer(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
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
