import { initBurger } from "./modules/burger.js";
import { initGallery } from "./modules/gallery.js";
import { initStepper } from "./modules/stepper.js";
import { initMenuFilters } from "./modules/filter.js";
import { initProfil } from "./modules/profil.js";

document.addEventListener("DOMContentLoaded", () => {
  initBurger();
  initGallery();
  initStepper();
  initProfil();
  initMenuFilters();
});
