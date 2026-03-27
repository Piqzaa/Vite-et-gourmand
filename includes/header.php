<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Découvrez tous les menus traiteur de Vite & Gourmand — filtrez par thème, régime, prix et nombre de personnes."
    />
    <title>Nos menus — Vite & Gourmand</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="assets/js/main.js" type="module" defer></script>
  </head>
  <header class="navbar">
      <div class="navbar__container">
        <a
          href="index.html"
          class="navbar__logo"
          aria-label="Vite & Gourmand — Accueil"
        >
          Vite <em>&</em> Gourmand
        </a>

        <nav class="navbar__nav" aria-label="Navigation principale">
          <ul class="navbar__links">
            <li><a href="index.html" class="navbar__link">Accueil</a></li>
            <li>
              <a href="menus.html" class="navbar__link navbar__link--active"
                >Nos menus</a
              >
            </li>
            <li><a href="contact.html" class="navbar__link">Contact</a></li>
          </ul>
        </nav>

        <a href="connexion.html" class="navbar__cta">Connexion</a>

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
          <li><a href="index.html">Accueil</a></li>
          <li><a href="menus.html">Nos menus</a></li>
          <li><a href="contact.html">Contact</a></li>
          <li><a href="connexion.html" class="navbar__cta">Connexion</a></li>
        </ul>
      </nav>
    </header>