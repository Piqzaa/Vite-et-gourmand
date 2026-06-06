<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\LoggerService;

class AuthController {
    public function __construct(
        private AuthService $authService,
        private LoggerService $logger
    ) {}

    /**
     * Affiche la page de connexion
     */
    public function loginPage(): void {
        if ($this->authService->isConnected()) {
            header('Location: index.php?page=home');
            exit;
        }

        $title = 'Connexion';
        $description = 'Connectez-vous à votre espace client Vite & Gourmand.';
        
        require __DIR__ . '/../../views/login.php';
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    public function login(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->authService->login($email, $password)) {
            $this->logger->log('auth_success', ['email' => $email]);
            
            // Redirection selon le rôle
            $redirect = 'index.php?page=home';
            if ($this->authService->isAdmin()) $redirect = 'espace-admin.php';
            elseif ($this->authService->isEmploye()) $redirect = 'espace-employe.php';
            
            header("Location: $redirect");
        } else {
            $this->logger->log('auth_failed', ['email' => $email]);
            header('Location: index.php?page=login&error=1');
        }
        exit;
    }

    /**
     * Déconnexion
     */
    public function logout(): void {
        $this->authService->logout();
        header('Location: index.php?page=home');
        exit;
    }
}
