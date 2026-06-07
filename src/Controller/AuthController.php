<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\LoggerService;
use App\Repository\UserRepository;

class AuthController {
    public function __construct(
        private AuthService $authService,
        private LoggerService $logger,
        private UserRepository $userRepository
    ) {}

    /**
     * Affiche la page de connexion
     */
    public function loginPage(): void {
        if ($this->authService->isConnected()) {
            header('Location: index.php?page=home');
            exit;
        }

        $redirect = $_GET['redirect'] ?? '';
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
        $redirectUrl = $_POST['redirect'] ?? '';

        if ($this->authService->login($email, $password)) {
            $this->logger->log('auth_success', ['email' => $email]);
            
            // Si une URL de redirection est fournie, on l'utilise
            if (!empty($redirectUrl)) {
                header("Location: $redirectUrl");
                exit;
            }

            // Sinon redirection par défaut selon le rôle
            $redirect = 'index.php?page=home';
            if ($this->authService->isAdmin()) $redirect = 'index.php?page=espace-admin'; // À adapter selon les routes
            elseif ($this->authService->isEmploye()) $redirect = 'index.php?page=espace-employe';
            
            header("Location: $redirect");
        } else {
            $this->logger->log('auth_failed', ['email' => $email]);
            $url = 'index.php?page=login&error=1';
            if (!empty($redirectUrl)) {
                $url .= '&redirect=' . urlencode($redirectUrl);
            }
            header("Location: $url");
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

    /**
     * Affiche la page d'inscription
     */
    public function registerPage(): void {
        if ($this->authService->isConnected()) {
            header('Location: index.php?page=home');
            exit;
        }

        $title = 'Inscription';
        $description = 'Créez votre compte Vite & Gourmand pour commander nos menus traiteur.';
        
        require __DIR__ . '/../../views/register.php';
    }

    /**
     * Traite l'inscription
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=register');
            exit;
        }

        $data = [
            'nom'             => trim($_POST['nom'] ?? ''),
            'prenom'          => trim($_POST['prenom'] ?? ''),
            'email'           => trim($_POST['email'] ?? ''),
            'password'        => $_POST['password'] ?? '',
            'gsm'             => trim($_POST['gsm'] ?? ''),
            'adresse_postale' => trim($_POST['adresse'] ?? ''),
            'ville'           => trim($_POST['ville'] ?? ''),
            'role'            => 'utilisateur',
            'actif'           => 1
        ];

        // Validation basique (à enrichir)
        if (empty($data['email']) || empty($data['password'])) {
            header('Location: index.php?page=register&error=champs_vides');
            exit;
        }

        // Hashage du mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($this->userRepository->save($data)) {
            $this->logger->log('user_registered', ['email' => $data['email']]);
            header('Location: index.php?page=login&success=registered');
        } else {
            header('Location: index.php?page=register&error=save_failed');
        }
        exit;
    }
}
