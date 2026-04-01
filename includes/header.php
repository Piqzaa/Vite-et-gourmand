<?php 
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
          <?php foreach (getRoleMenuLinks() as $link): ?>
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
          <?= nav_itemMobil('index.php', 'Accueil') ?>
          <?= nav_itemMobil('menus.php', 'Nos menus') ?>
          <?= nav_itemMobil('contact.php', 'Contact') ?>
          <?php foreach (getRoleMenuLinks() as $link): ?>
            <?= nav_itemMobil($link[0], $link[1]) ?>
          <?php endforeach; ?>

        </ul>
      </nav>
    </header>
