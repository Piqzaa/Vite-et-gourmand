<?php 
session_start();
require_once __DIR__ . '/functions.php'; 
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
            <?= nav_item('index.php', 'Accueil') ?>
            <?= nav_item('menus.php', 'Nos menus') ?>
            <?= nav_item('contact.php', 'Contact') ?>
          </ul>
        </nav>
        <div class="navbar__spacer">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="espace-utilisateur.php" class="navbar__cta--secondary">
              <?= htmlspecialchars($_SESSION['user_prenom']) ?>
          </a>
          <a href="assets/php/auth/logout.php" class="navbar__cta">Déconnexion</a>
        <?php else: ?>
            <a href="connexion.php" class="navbar__cta">Connexion</a>
            <a href="inscription.php" class="navbar__cta--secondary">Inscription</a>
        <?php endif; ?>
        </div>
        

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
