<?php
use App\Helper\ViewHelper;
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
            <?= ViewHelper::navItem('index.php?page=home', 'Accueil') ?>
            <?= ViewHelper::navItem('index.php?page=menus', 'Nos menus') ?>
            <?= ViewHelper::navItem('index.php?page=contact', 'Contact') ?>
          </ul>
        </nav>
        <div class="navbar__spacer">
          <?php foreach (ViewHelper::getRoleMenuLinks() as $link): ?>
            <a href="<?= $link[0] ?>" class="<?= $link[2] ?>"><?= $link[1] ?></a>
          <?php endforeach; ?>
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
          <?= ViewHelper::navItemMobile('index.php?page=home', 'Accueil') ?>
          <?= ViewHelper::navItemMobile('index.php?page=menus', 'Nos menus') ?>
          <?= ViewHelper::navItemMobile('index.php?page=contact', 'Contact') ?>
          <?php foreach (ViewHelper::getRoleMenuLinks() as $link): ?>
            <?= ViewHelper::navItemMobile($link[0], $link[1]) ?>
          <?php endforeach; ?>

        </ul>
      </nav>
    </header>
