<?php

function nav_item(string $lien, string $titre): string
{
    $classe = 'navbar__link';
    if ((basename($_SERVER['SCRIPT_NAME']) === $lien)) {
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
            ['connexion.php', 'Connexion 🔐', 'navbar__cta'],
            ['inscription.php', 'Inscription 📝', 'navbar__cta--secondary']
        ];
    }

    switch ($_SESSION['user_role']) {
        case 'admin':
            return [
                ['espace-admin.php', 'Administration 🛠️', 'navbar__cta--secondary'],
                ['assets/php/auth/logout.php', 'Déconnexion ➜]', 'navbar__cta']
            ];

        case 'employe':
            return [
                ['espace-employe.php', 'Espace employé 🧑‍🍳', 'navbar__cta--secondary'],
                ['assets/php/auth/logout.php', 'Déconnexion ➜]', 'navbar__cta']
            ];

        default: // user
            return [
                ['espace-utilisateur.php', 'Mon compte 👤', 'navbar__cta--secondary'],
                ['assets/php/auth/logout.php', 'Déconnexion ➜]', 'navbar__cta']
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

?>

