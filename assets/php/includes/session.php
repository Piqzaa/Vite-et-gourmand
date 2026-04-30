<?php

function sessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configuration sécurisée des cookies de session
        ini_set('session.cookie_httponly', '1');      // inaccessible via JavaScript
        ini_set('session.cookie_secure', '0');        // HTTPS uniquement (désactiver en local sans HTTPS)
        ini_set('session.use_strict_mode', '1');      // rejette les IDs de session inconnus
        ini_set('session.cookie_samesite', 'Strict'); // empêche l'envoi cross-site

        session_start();
    }
}

function isConnected(): bool {
    return isset($_SESSION['user_id']);
}

function getUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Génère et retourne un token CSRF pour protéger les formulaires
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF d'un formulaire
 * @return bool true si valide, false sinon
 */
function validateCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Régénère le token CSRF (à utiliser après une action sensible)
 */
function regenerateCsrfToken(): string {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
