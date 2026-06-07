<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;

class AuthService {
    public function __construct(private UserRepository $userRepository) {}

    public function login(string $email, string $password): bool {
        $user = $this->userRepository->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['actif']) {
                return false;
            }
            
            $_SESSION['user_id'] = $user['utilisateur_id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
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
