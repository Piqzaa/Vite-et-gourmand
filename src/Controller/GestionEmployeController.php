<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\SecurityService;

class GestionEmployeController {
    public function __construct(
        private UserRepository $userRepo,
        private AuthService $authService,
        private SecurityService $securityService
    ) {}

    public function create(): void {
        if (!$this->authService->isAdmin()) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-admin&error=csrf_invalid#employes');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        // Vérification unicité email
        if ($this->userRepo->findByEmail($email)) {
            header('Location: index.php?page=espace-admin&error=email_existant#employes');
            exit;
        }

        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => $email,
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

        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=espace-admin&error=csrf_invalid#employes');
            exit;
        }

        $id = (int)($_POST['employe_id'] ?? 0);
        $actif = (bool)($_POST['actif'] ?? 0);

        $this->userRepo->updateActif($id, $actif);
        header('Location: index.php?page=espace-admin&success=employe_mis_a_jour#employes');
        exit;
    }
}
