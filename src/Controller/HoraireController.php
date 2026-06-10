<?php
namespace App\Controller;

use App\Repository\HoraireRepository;
use App\Service\AuthService;

class HoraireController {
    public function __construct(
        private HoraireRepository $horaireRepo,
        private AuthService $authService
    ) {}

    public function update(): void {
        if (!$this->authService->isEmploye()) {
            header('Location: index.php?page=login');
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
