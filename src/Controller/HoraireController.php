<?php
namespace App\Controller;

use App\Repository\HoraireRepository;
use App\Service\AuthService;
use App\Service\SecurityService;

class HoraireController {
    public function __construct(
        private HoraireRepository $horaireRepo,
        private AuthService $authService,
        private SecurityService $securityService
    ) {}

    public function update(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-employe&error=csrf_invalid#horaires');
            exit;
        }

        $horaires = $_POST['horaire'] ?? [];
        foreach ($horaires as $id => $valeurs) {
            $this->horaireRepo->update((int)$id, $valeurs['ouverture'], $valeurs['fermeture']);
        }

        $redirect = $this->authService->isAdmin() ? 'espace-admin' : 'espace-employe';
        header("Location: index.php?page=$redirect&success=horaires_mis_a_jour#horaires");
        exit;
    }
}
