<?php

namespace App\Service;

class SecurityService {
    /**
     * Génère et retourne un token CSRF
     */
    public function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le token CSRF
     */
    public function validateCsrfToken(?string $token): bool {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Régénère le token CSRF
     */
    public function regenerateCsrfToken(): string {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    /**
     * Génère et retourne un token de déconnexion (anti-CSRF)
     */
    public function getLogoutToken(): string {
        if (empty($_SESSION['logout_token'])) {
            $_SESSION['logout_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['logout_token'];
    }

    /**
     * Vérifie le token de déconnexion
     */
    public function validateLogoutToken(?string $token): bool {
        if (empty($token) || empty($_SESSION['logout_token'])) {
            return false;
        }
        return hash_equals($_SESSION['logout_token'], $token);
    }

    /**
     * Invalide le token de déconnexion après usage
     */
    public function clearLogoutToken(): void {
        unset($_SESSION['logout_token']);
    }
}
