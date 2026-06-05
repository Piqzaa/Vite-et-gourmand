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
    const article = document.createElement("article");
    article.className = "menu-card";

    const imageSrc = menu.image_path
      ? `assets/img/plats/${menu.image_path}`
      : "assets/img/menu-placeholder.jpg";

    const imgWrapper = document.createElement("div");
    imgWrapper.className = "menu-card__img-wrapper";

    const img = document.createElement("img");
    img.src = imageSrc;
    img.alt = menu.titre;
    img.className = "menu-card__img";
    img.onerror = function () {
      this.onerror = null;
      this.src = "assets/img/menu-placeholder.jpg";
    };
    imgWrapper.appendChild(img);

    const body = document.createElement("div");
    body.className = "menu-card__body";

    const tag = document.createElement("span");
    tag.className = "menu-card__tag";
    tag.textContent = menu.theme ?? "—";

    const title = document.createElement("h3");
    title.className = "menu-card__title";
    title.textContent = menu.titre;

    const desc = document.createElement("p");
    desc.className = "menu-card__desc";
    desc.textContent = menu.description;

    const meta = document.createElement("div");
    meta.className = "menu-card__meta";

    const price = document.createElement("span");
    price.className = "menu-card__price";
    price.textContent = `À partir de ${menu.prix_base}€`;

    const persons = document.createElement("span");
    persons.className = "menu-card__persons";
    persons.textContent = `${menu.nombre_personne_min} pers. min.`;

    meta.append(price, persons);

    const link = document.createElement("a");
    link.href = `menu-detail.php?id=${menu.menu_id}`;
    link.className = "btn btn--outline btn--full";
    link.textContent = "Voir le détail";

    body.append(tag, title, desc, meta, link);
    article.append(imgWrapper, body);

    return article;
  }

  async function fetchMenus() {
    const params = buildParams();
    try {
      const res = await fetch(`assets/php/api/menus.php?${params}`);
      const data = await res.json();

      // On vide TOUTE la grille avant de ré-afficher
      grid.replaceChildren();

      if (data.length === 0) {
        grid.appendChild(emptyMsg); // On remet le message d'erreur dans la grille si besoin
        emptyMsg.hidden = false;
        countEl.textContent = "0";
      } else {
        emptyMsg.hidden = true;
        countEl.textContent = data.length;
        data.forEach((menu) => {
          grid.appendChild(renderCard(menu));
        });
      }
    } catch (err) {
      console.error("Erreur :", err);
    }
  }

  fetchMenus();
}
