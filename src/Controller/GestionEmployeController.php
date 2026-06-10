<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AuthService;

class GestionEmployeController {
    public function __construct(
        private UserRepository $userRepo,
        private AuthService $authService
    ) {}

    public function create(): void {
        if (!$this->authService->isAdmin()) {
            header('Location: index.php?page=login');
            exit;
        }

        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'gsm' => '',
            'adresse_postale' => '',
            'ville' => '',
            'role' => 'employe',
            'actif' => 1
        ];

        if ($this->userRepo->save($data)) {
            header('Location: index.php?page=espace-admin&success=employe_cree#employes');
        } else {
            header('Location: index.php?page=espace-admin&error=save_failed#employes');
        }
        exit;
    }

    public function toggle(): void {
        if (!$this->authService->isAdmin()) {
            header('Location: index.php?page=login');
            exit;
        }

        $id = (int)($_POST['employe_id'] ?? 0);
        $actif = (bool)($_POST['actif'] ?? 0);

        $this->userRepo->updateActif($id, $actif);
        header('Location: index.php?page=espace-admin&success=employe_mis_a_jour#employes');
        exit;
    }
}
