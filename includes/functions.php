<?php

function nav_item(string $lien, string $titre): string
{
    $classe = 'navbar__link';
    $currentPage = $_GET['page'] ?? 'home';
    
    // Si le lien est index.php, c'est la home
    if ($lien === 'index.php' && $currentPage === 'home') {
        $classe .= ' navbar__link--active';
    } 
    // Lien Nos menus
    elseif ($lien === 'menus.php' && $currentPage === 'menus') {
        $classe .= ' navbar__link--active';
    }
    elseif (basename($_SERVER['SCRIPT_NAME']) === $lien && !isset($_GET['page'])) {
        $classe .= ' navbar__link--active';
    }

    return <<<HTML
        <li>
            <a href="$lien" class="$classe">$titre</a>
        </li>
    HTML;
}

function nav_itemMobil(string $lien, string $titre): string
{
    return <<<HTML
        <li>
            <a href="$lien">$titre</a>
        </li>
    HTML;
}

function getRoleMenuLinks(): array {
    if (!isset($_SESSION['user_id'])) {
        return [
            ['index.php?page=login', 'Connexion 🔐', 'navbar__cta'],
            ['index.php?page=register', 'Inscription 📝', 'navbar__cta--secondary']
        ];
    }

    switch ($_SESSION['user_role']) {
        case 'admin':
            return [
                ['index.php?page=espace-admin', 'Administration 🛠️', 'navbar__cta--secondary'],
                ['index.php?page=logout', 'Déconnexion ➜]', 'navbar__cta']
            ];

        case 'employe':
            return [
                ['index.php?page=espace-employe', 'Espace employé 🧑‍🍳', 'navbar__cta--secondary'],
                ['index.php?page=logout', 'Déconnexion ➜]', 'navbar__cta']
            ];

        default: // user
            return [
                ['index.php?page=espace-utilisateur', 'Mon compte 👤', 'navbar__cta--secondary'],
                ['index.php?page=logout', 'Déconnexion ➜]', 'navbar__cta']
            ];
    }
}

function getStatusClass(string $status): string {
    return match($status) {
        'en attente' => 'commande-card__status--en-attente',
        'accepté' => 'commande-card__status--accepte',
        'en préparation' => 'commande-card__status--en-preparation',
        'en cours de livraison' => 'commande-card__status--en-livraison',
        'livré' => 'commande-card__status--accepte',
        'en attente retour matériel' => 'commande-card__status--en-attente',
        'terminée' => 'commande-card__status--terminee',
        'annulée' => 'commande-card__status--annulee',
        default => ''
    };
}



