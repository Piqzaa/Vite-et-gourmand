
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
