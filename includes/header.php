<?php

function nav_item(string $lien, string $titre, string $current): string
{
    $classe = 'navbar__link';
    if ($current === $lien) {
        $classe .= ' navbar__link--active';
    }

    return <<<HTML
        <li>
            <a href="$lien" class="$classe">$titre</a>
        </li>
    HTML;
}

$current = basename($_SERVER['SCRIPT_NAME']);
?>

  <header class="navbar">
      <div class="navbar__container">
        <a
          href="index.php"
          class="navbar__logo"
          aria-label="Vite & Gourmand — Accueil"
        >
          Vite <em>&</em> Gourmand
        </a>
        <nav class="navbar__nav" aria-label="Navigation principale">
          <ul class="navbar__links">
            <?= nav_item('index.php', 'Accueil', $current) ?>
            <?= nav_item('menus.php', 'Nos menus', $current) ?>
            <?= nav_item('contact.php', 'Contact', $current) ?>
          </ul>
        </nav>
        <a href="connexion.php" class="navbar__cta">Connexion</a>

        <button
          class="navbar__burger"
          aria-label="Ouvrir le menu"
          aria-expanded="false"
          aria-controls="mobile-menu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>

      <nav class="navbar__mobile" id="mobile-menu" aria-hidden="true">
        <ul class="navbar__mobile-links">
          <li><a href="index.php">Accueil</a></li>
          <li><a href="menus.php">Nos menus</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="connexion.php" class="navbar__cta">Connexion</a></li>
        </ul>
      </nav>
    </header>
