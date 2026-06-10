<?php

namespace App\Helper;

class ViewHelper {
    /**
     * Génère un item de navigation desktop
     */
    public static function navItem(string $lien, string $titre): string {
        $classe = 'navbar__link';
        $currentPage = $_GET['page'] ?? 'home';
        
        if (($lien === 'index.php?page=home' && $currentPage === 'home') || 
            ($lien === 'index.php?page=menus' && $currentPage === 'menus')) {
            $classe .= ' navbar__link--active';
        }

        return "<li><a href=\"$lien\" class=\"$classe\">$titre</a></li>";
    }

    /**
     * Génère un item de navigation mobile
     */
    public static function navItemMobile(string $lien, string $titre): string {
        return "<li><a href=\"$lien\">$titre</a></li>";
    }

    /**
     * Retourne la classe CSS selon le statut de la commande
     */
    public static function getStatusClass(string $status): string {
        return match($status) {
            'en attente' => 'commande-card__status--en-attente',
            'accepté' => 'commande-card__status--accepte',
            'en préparation' => 'commande-card__status--en-preparation',
            'en cours de livraison' => 'commande-card__status--en-livraison',
            'livré' => 'commande-card__status--accepte',
            'en attente du retour de matériel' => 'commande-card__status--en-attente',
            'terminée' => 'commande-card__status--terminee',
            'annulée' => 'commande-card__status--annulee',
            default => ''
        };
    }

    /**
     * Retourne l'URL de déconnexion avec token anti-CSRF
     */
    private static function getLogoutUrl(): string {
        if (empty($_SESSION['logout_token'])) {
            $_SESSION['logout_token'] = bin2hex(random_bytes(32));
        }
        return 'index.php?page=logout&token=' . $_SESSION['logout_token'];
    }

    /**
     * Génère les liens de navigation selon le rôle
     */
    public static function getRoleMenuLinks(): array {
        if (!isset($_SESSION['user_id'])) {
            return [
                ['index.php?page=login', 'Connexion 🔐', 'navbar__cta'],
                ['index.php?page=register', 'Inscription 📝', 'navbar__cta--secondary']
            ];
        }

        return match ($_SESSION['user_role'] ?? 'user') {
            'admin' => [
                ['index.php?page=espace-admin', 'Administration 🛠️', 'navbar__cta--secondary'],
                [self::getLogoutUrl(), 'Déconnexion ➜]', 'navbar__cta']
            ],
            'employe' => [
                ['index.php?page=espace-employe', 'Espace employé 🧑‍🍳', 'navbar__cta--secondary'],
                [self::getLogoutUrl(), 'Déconnexion ➜]', 'navbar__cta']
            ],
            default => [
                ['index.php?page=espace-utilisateur', 'Mon compte 👤', 'navbar__cta--secondary'],
                [self::getLogoutUrl(), 'Déconnexion ➜]', 'navbar__cta']
            ],
        };
    }
}
