export function initMenuFilters() {
  const grid = document.getElementById("menus-grid");

  // Si la grille n'existe pas (ex: on est sur une autre page), on coupe tout de suite
  if (!grid) return;

  const emptyMsg = document.getElementById("menus-empty");
  const countEl = document.getElementById("menus-count");

  // Inputs
  const rangeMax = document.getElementById("filter-prix-max");
  const rangeLabel = document.getElementById("prix-max-value");
  const inputMin = document.getElementById("filter-prix-min");
  const inputMax = document.getElementById("filter-prix-max2");
  const selectTheme = document.getElementById("filter-theme");
  const selectRegime = document.getElementById("filter-regime");
  const inputPersons = document.getElementById("filter-personnes");
  const btnReset = document.getElementById("filters-reset");
  const btnResetEmpty = document.getElementById("reset-from-empty");

  // Événements
  // Synchronisation Range -> Input Nombre
  rangeMax.addEventListener("input", () => {
    rangeLabel.textContent = rangeMax.value + "€";
    inputMax.value = rangeMax.value; // Le champ nombre s'aligne
    fetchMenus();
  });

  // Synchronisation Input Nombre -> Range
  inputMax.addEventListener("input", () => {
    rangeMax.value = inputMax.value; // Le slider s'aligne
    rangeLabel.textContent = inputMax.value + "€";
    fetchMenus();
  });

  // Pour les autres filtres
  [inputMin, selectTheme, selectRegime, inputPersons].forEach((el) => {
    el.addEventListener("input", fetchMenus);
  });

  btnReset.addEventListener("click", resetFilters);
  btnResetEmpty.addEventListener("click", resetFilters);

  // Fonctions internes à l'initialisation
  function resetFilters() {
    rangeMax.value = 200;
    rangeLabel.textContent = "200€";
    inputMin.value = "";
    inputMax.value = "";
    selectTheme.value = "";
    selectRegime.value = "";
    inputPersons.value = "";
    fetchMenus();
  }

  function buildParams() {
    const params = new URLSearchParams();

    if (rangeMax.value && rangeMax.value < 200) {
      params.set("prix_max", rangeMax.value);
    }

    if (inputMin.value && inputMin.value > 0) {
      params.set("prix_min", inputMin.value);
    }

    if (selectTheme.value) params.set("theme", selectTheme.value);
    if (selectRegime.value) params.set("regime", selectRegime.value);
    if (inputPersons.value) params.set("personnes", inputPersons.value);

    return params;
  }
  function renderCard(menu) {
    return `
        <article class="menu-card">
            <div class="menu-card__img-wrapper">
                <img
                    src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600"
                    alt="${escHtml(menu.titre)}"
                    class="menu-card__img"
                />
            </div>
            <div class="menu-card__body">
                <span class="menu-card__tag">${escHtml(menu.theme ?? "—")}</span>
                <h3 class="menu-card__title">${escHtml(menu.titre)}</h3>
                <p class="menu-card__desc">${escHtml(menu.description)}</p>
                <div class="menu-card__meta">
                    <span class="menu-card__price">À partir de ${menu.prix_base}€</span>
                    <span class="menu-card__persons">${menu.nombre_personne_min} pers. min.</span>
                </div>
                <a href="menu-detail.php?id=${menu.menu_id}" class="btn btn--outline btn--full">
                    Voir le détail
                </a>
            </div>
        </article>`;
  }

  function escHtml(str) {
    const d = document.createElement("div");
    d.textContent = str ?? "";
    return d.innerHTML;
  }

  async function fetchMenus() {
    const params = buildParams();
    try {
      const res = await fetch(`assets/php/api/menus.php?${params}`);
      const data = await res.json();

      // On vide TOUTE la grille avant de ré-afficher
      grid.innerHTML = "";

      if (data.length === 0) {
        grid.appendChild(emptyMsg); // On remet le message d'erreur dans la grille si besoin
        emptyMsg.hidden = false;
        countEl.textContent = "0";
      } else {
        emptyMsg.hidden = true;
        countEl.textContent = data.length;
        data.forEach((menu) => {
          grid.insertAdjacentHTML("beforeend", renderCard(menu));
        });
      }
    } catch (err) {
      console.error("Erreur :", err);
    }
  }
}
