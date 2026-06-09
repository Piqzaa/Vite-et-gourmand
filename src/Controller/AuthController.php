<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\LoggerService;
use App\Repository\UserRepository;
use App\Service\MailService;

class AuthController {
    public function __construct(
        private AuthService $authService,
        private LoggerService $logger,
        private UserRepository $userRepository,
        private MailService $mailService
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
            
            if (!empty($redirectUrl)) {
                header("Location: $redirectUrl");
                exit;
            }

            $redirect = 'index.php?page=home';
            if ($this->authService->isAdmin()) $redirect = 'index.php?page=espace-admin';
            elseif ($this->authService->isEmploye()) $redirect = 'index.php?page=espace-employe';
            elseif ($this->authService->isUser()) $redirect = 'index.php?page=espace-utilisateur';
            
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
        $title = 'Inscription';
        $description = 'Créez votre compte client Vite & Gourmand.';
        require __DIR__ . '/../../views/register.php';
    }

    /**
     * Traite l'inscription
     */
    public function register(): void {
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $_POST['email'] ?? '',
            'gsm' => $_POST['gsm'] ?? '',
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'adresse_postale' => $_POST['adresse_postale'] ?? '',
            'ville' => $_POST['ville'] ?? '',
            'role' => 'utilisateur',
            'actif' => 1
        ];

        if ($this->userRepository->save($data)) {
            $this->logger->log('user_registered', ['email' => $data['email']]);
            header('Location: index.php?page=login&success=registered');
        } else {
            header('Location: index.php?page=register&error=save_failed');
        }
        exit;
    }

    /**
     * Traite la demande de mot de passe oublié
     */
    public function forgotPassword(): void {
        $email = trim($_POST['email'] ?? '');
        if (!$email) {
            header('Location: index.php?page=login&error=email_manquant');
            exit;
        }

        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->userRepository->setResetToken($user->getId(), $token, $expire);

            $resetUrl = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?page=reset-password&token=" . $token;
            $htmlBody = "<h1>Réinitialisation de mot de passe</h1>
                         <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
                         <p><a href='$resetUrl'>$resetUrl</a></p>
                         <p>Ce lien expirera dans 1 heure.</p>";
            
            $this->mailService->send($user->getEmail(), $user->getPrenom() . ' ' . $user->getNom(), 'Réinitialisation de mot de passe', $htmlBody);
        }

        // On redirige toujours avec un message de succès pour éviter le user enumeration
        header('Location: index.php?page=login&success=reset_sent');
        exit;
    }

    /**
     * Affiche la page de réinitialisation de mot de passe
     */
    public function resetPasswordPage(): void {
        $token = trim($_GET['token'] ?? '');
        if (!$token) {
            header('Location: index.php?page=login&error=token_manquant');
            exit;
        }

        $user = $this->userRepository->findByResetToken($token);
        if (!$user) {
            header('Location: index.php?page=login&error=token_invalide');
            exit;
        }

        $title = 'Réinitialisation du mot de passe';
        require __DIR__ . '/../../views/reset-password.php';
    }

    /**
     * Traite la réinitialisation du mot de passe
     */
    public function resetPassword(): void {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if ($password !== $confirm) {
            header("Location: index.php?page=reset-password&token=$token&error=mismatch");
            exit;
        }

        // Validation complexité mot de passe
        if (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            header("Location: index.php?page=reset-password&token=$token&error=not_complex");
            exit;
        }

        $user = $this->userRepository->findByResetToken($token);
        if (!$user) {
            header('Location: index.php?page=login&error=token_expire');
            exit;
        }

        if ($this->userRepository->updatePassword($user->getId(), password_hash($password, PASSWORD_DEFAULT))) {
            $this->logger->log('password_reset', ['user_id' => $user->getId()]);
            header('Location: index.php?page=login&success=password_updated');
        } else {
            header("Location: index.php?page=reset-password&token=$token&error=db_error");
        }
        exit;
    }
}
