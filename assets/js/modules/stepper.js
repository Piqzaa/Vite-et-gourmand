export function initStepper() {
  const steps = document.querySelectorAll(".commande-step");
  const stepperItems = document.querySelectorAll(".stepper__item");
  const aside = document.querySelector(".commande-aside");
  const formContainer = document.querySelector(".commande-page__container");

  const btnNext = document.getElementById("btn-next");
  const btnPrev = document.getElementById("btn-prev");
  const btnSubmit = document.getElementById("btn-submit");

  const stepperElement = document.querySelector(".stepper");
  if (!stepperElement) return;

  // Éléments calcul prix
  const menuSelect = document.getElementById("menu-choisi");
  const nbPersonnes = document.getElementById("nb-personnes");
  const villeInput = document.getElementById("ville-livraison");

  let currentStep = 0;

  // ─── Calcul prix ────────────────────────────────────────────
  function calculPrix() {
    if (!menuSelect || !nbPersonnes) return null;

    const option = menuSelect.options[menuSelect.selectedIndex];
    const prixBase = parseFloat(option.dataset.prix) || 0;
    const minPersons = parseInt(option.dataset.min) || 1;
    const nb = parseInt(nbPersonnes.value) || 0;
    const ville = villeInput ? villeInput.value.toLowerCase() : "";

    if (!prixBase || nb < minPersons) return null;

    const prixParPers = prixBase / minPersons;
    let prixMenu = prixParPers * nb;
    let reduction = 0;
    const livraison = ville.includes("bordeaux") ? 0 : 5;

    if (nb >= minPersons + 5) {
      reduction = prixMenu * 0.1;
      prixMenu -= reduction;
    }

    return {
      menu: prixMenu.toFixed(2),
      reduction: reduction.toFixed(2),
      livraison: livraison.toFixed(2),
      total: (prixMenu + livraison).toFixed(2),
    };
  }

  // ─── Mise à jour aside (sticky) ─────────────────────────────
  function updateAside() {
    const prix = calculPrix();
    if (!prix) return;

    const option = menuSelect.options[menuSelect.selectedIndex];

    document.getElementById("aside-menu").textContent = option.text
      .split("—")[0]
      .trim();
    document.getElementById("aside-personnes").textContent =
      nbPersonnes.value + " pers.";
    document.getElementById("aside-livraison").textContent =
      prix.livraison === "0.00" ? "Gratuit" : prix.livraison + "€";
    document.getElementById("aside-reduction").textContent =
      prix.reduction === "0.00" ? "—" : "-" + prix.reduction + "€";
    document.getElementById("aside-total").textContent = prix.total + "€";
  }

  // ─── Mise à jour récap étape 3 ──────────────────────────────
  function updateRecap() {
    const prix = calculPrix();
    if (!prix) return;

    const option = menuSelect.options[menuSelect.selectedIndex];
    const adresse = document.getElementById("adresse-livraison")?.value ?? "—";
    const date = document.getElementById("date-livraison")?.value ?? "—";

    document.getElementById("recap-menu").textContent = option.text
      .split("—")[0]
      .trim();
    document.getElementById("recap-personnes").textContent =
      nbPersonnes.value + " pers.";
    document.getElementById("recap-date").textContent = date;
    document.getElementById("recap-adresse").textContent = adresse;
    document.getElementById("recap-prix-menu").textContent = prix.menu + "€";
    document.getElementById("recap-livraison").textContent =
      prix.livraison === "0.00" ? "Gratuit" : prix.livraison + "€";
    document.getElementById("recap-reduction").textContent =
      prix.reduction === "0.00" ? "—" : "-" + prix.reduction + "€";
    document.getElementById("recap-total").textContent = prix.total + "€";

    document.getElementById("recap-reduction-row").style.display =
      prix.reduction === "0.00" ? "none" : "flex";
  }

  // ─── Événements menu + personnes ────────────────────────────
  if (menuSelect) {
    menuSelect.addEventListener("change", () => {
      const option = menuSelect.options[menuSelect.selectedIndex];
      const minPersons = parseInt(option.dataset.min) || 1;
      const conditions = option.dataset.conditions ?? "";

      // Met à jour le minimum requis
      nbPersonnes.min = minPersons;
      nbPersonnes.value = minPersons;
      document.getElementById("personnes-hint").textContent =
        "Minimum requis : " + minPersons + " personnes";

      // Affiche les conditions du menu
      const condBlock = document.getElementById("commande-conditions");
      const condList = condBlock.querySelector(".menu-conditions__list");
      if (conditions) {
        condList.innerHTML = `<li>${conditions}</li>`;
        condBlock.hidden = false;
      } else {
        condBlock.hidden = true;
      }

      updateAside();
    });
  }

  if (nbPersonnes) {
    nbPersonnes.addEventListener("input", updateAside);
  }

  if (villeInput) {
    villeInput.addEventListener("input", updateAside);
  }

  // ─── Navigation stepper ─────────────────────────────────────
  function showStep(index) {
    steps.forEach((step, i) => {
      step.style.display = i === index ? "block" : "none";
    });

    stepperItems.forEach((item, i) => {
      item.classList.toggle("stepper__item--active", i === index);
    });

    btnPrev.style.display = index === 0 ? "none" : "inline-flex";
    btnNext.style.display = index === steps.length - 1 ? "none" : "inline-flex";
    btnSubmit.style.display =
      index === steps.length - 1 ? "inline-flex" : "none";

    aside.style.display = index === steps.length - 1 ? "none" : "block";
    formContainer.style.gridTemplateColumns =
      index === steps.length - 1 ? "1fr" : "1fr 320px";

    // Quand on arrive à l'étape 3, on remplit le récap
    if (index === steps.length - 1) {
      updateRecap();
    }
  }

  function validateStep(index) {
    const step = steps[index];
    const requiredFields = step.querySelectorAll("[required]");

    for (const field of requiredFields) {
      if (!field.value.trim()) {
        field.classList.add("form-input--error");
        field.focus();
        return false;
      }
      field.classList.remove("form-input--error");
    }

    return true;
  }

  btnNext.addEventListener("click", () => {
    if (!validateStep(currentStep)) return;
    currentStep++;
    showStep(currentStep);
  });

  btnPrev.addEventListener("click", () => {
    currentStep--;
    showStep(currentStep);
  });

  showStep(currentStep);
}
