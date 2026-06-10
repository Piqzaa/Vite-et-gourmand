<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;

class AuthService {
    public function __construct(private UserRepository $userRepository) {}

    public function login(string $email, string $password): bool {
        $user = $this->userRepository->findByEmail($email);
        
        if ($user && password_verify($password, $user->getPassword())) {
            if (!$user->isActif()) {
                return false;
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_nom'] = $user->getNom();
            $_SESSION['user_prenom'] = $user->getPrenom();
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_role'] = $user->getRole();
            
            return true;
        }
        
        return false;
    }

    public function logout(): void {
        session_unset();
        session_destroy();
    }

    public function isConnected(): bool {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool {
        return ($this->isConnected() && $_SESSION['user_role'] === 'admin');
    }

    public function isEmploye(): bool {
        return ($this->isConnected() && ($_SESSION['user_role'] === 'employe' || $_SESSION['user_role'] === 'admin'));
    }

    public function isUser(): bool {
        return ($this->isConnected() && $_SESSION['user_role'] === 'utilisateur');
    }
}
