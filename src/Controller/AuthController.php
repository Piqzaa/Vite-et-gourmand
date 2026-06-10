<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\LoggerService;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\SecurityService;

class AuthController {
    public function __construct(
        private AuthService $authService,
        private LoggerService $logger,
        private UserRepository $userRepository,
        private MailService $mailService,
        private SecurityService $securityService
    ) {}

    /**
     * Affiche la page de connexion
     */
    public function loginPage(): void {
        if ($this->authService->isConnected()) {
            header('Location: index.php?page=home');
            exit;
        }

        // On génère le token CSRF pour la page de login
        $this->securityService->generateCsrfToken();

        $redirect = $_GET['redirect'] ?? '';
        $title = 'Connexion';
        $description = 'Connectez-vous à votre espace client Vite & Gourmand.';
        
        require __DIR__ . '/../../views/login.php';
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    public function login(): void {
        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=login&error=csrf_invalid');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $redirectUrl = $_POST['redirect'] ?? '';

        if ($this->authService->login($email, $password)) {
            $this->logger->log('auth_success', ['email' => $email]);
            $this->securityService->regenerateCsrfToken(); // Sécurité : on change le token après login
            
            if (!empty($redirectUrl) && $this->isInternalRedirect($redirectUrl)) {
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
        $this->securityService->generateCsrfToken();
        $title = 'Inscription';
        $description = 'Créez votre compte client Vite & Gourmand.';
        require __DIR__ . '/../../views/register.php';
    }

    /**
     * Traite l'inscription
     */
    public function register(): void {
        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=register&error=csrf_invalid');
            exit;
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $gsm = trim($_POST['gsm'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $ville = trim($_POST['ville'] ?? '');

        // 1. Validations de base
        if (!$email || !$password || !$nom || !$prenom || !$gsm || !$adresse || !$ville) {
            header('Location: index.php?page=register&error=champs_manquants');
            exit;
        }

        // 2. Vérification mot de passe identique
        if ($password !== $confirm) {
            header('Location: index.php?page=register&error=password_mismatch');
            exit;
        }

        // 3. Force du mot de passe (min 10 car, 1 maj, 1 min, 1 chiffre, 1 spécial)
        if (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            header('Location: index.php?page=register&error=password_weak');
            exit;
        }

        // 4. Format GSM (simplifié)
        if (!preg_match('/^[0-9+ \.]{10,20}$/', $gsm)) {
            header('Location: index.php?page=register&error=gsm_invalid');
            exit;
        }

        // 5. Vérification email unique
        if ($this->userRepository->findByEmail($email)) {
            header('Location: index.php?page=register&error=email_exists');
            exit;
        }

        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'gsm' => $gsm,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'adresse_postale' => $adresse,
            'ville' => $ville,
            'role' => 'utilisateur',
            'actif' => 1
        ];

        if ($this->userRepository->save($data)) {
            $this->logger->log('user_registered', ['email' => $email]);
            $this->securityService->regenerateCsrfToken();
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
        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=login&error=csrf_invalid');
            exit;
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            header('Location: index.php?page=login&error=email_manquant');
            exit;
        }

        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->userRepository->setResetToken($user->getId(), $token, $expire);

            // SECU : valider le host pour éviter les injections dans l'email
            $host = $_SERVER['HTTP_HOST'];
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9.-]*(:\d+)?$/', $host)) {
                $host = 'localhost:8080';
            }
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $resetUrl = "$protocol://" . $host . "/index.php?page=reset-password&token=" . $token;
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
        $token = trim($_GET['token'] ?? $_SESSION['reset_token'] ?? '');
        unset($_SESSION['reset_token']);
        if (!$token) {
            header('Location: index.php?page=login&error=token_manquant');
            exit;
        }

        $user = $this->userRepository->findByResetToken($token);
        if (!$user) {
            header('Location: index.php?page=login&error=token_invalide');
            exit;
        }

        $this->securityService->generateCsrfToken();
        $title = 'Réinitialisation du mot de passe';
        require __DIR__ . '/../../views/reset-password.php';
    }

    /**
     * Traite la réinitialisation du mot de passe
     */
    public function resetPassword(): void {
        if (!$this->securityService->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?page=login&error=csrf_invalid');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if ($password !== $confirm) {
            $_SESSION['reset_token'] = $token;
            header("Location: index.php?page=reset-password&error=mismatch");
            exit;
        }

        // Validation complexité mot de passe
        if (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $_SESSION['reset_token'] = $token;
            header("Location: index.php?page=reset-password&error=not_complex");
            exit;
        }

        $user = $this->userRepository->findByResetToken($token);
        if (!$user) {
            header('Location: index.php?page=login&error=token_expire');
            exit;
        }

        if ($this->userRepository->updatePassword($user->getId(), password_hash($password, PASSWORD_DEFAULT))) {
            $this->logger->log('password_reset', ['user_id' => $user->getId()]);
            $this->securityService->regenerateCsrfToken();
            header('Location: index.php?page=login&success=password_updated');
        } else {
            $_SESSION['reset_token'] = $token;
            header("Location: index.php?page=reset-password&error=db_error");
        }
        exit;
    }

    private function isInternalRedirect(string $url): bool
    {
        return str_starts_with($url, 'index.php?page=');
    }
}
