import { initBurger } from "./modules/burger.js";
import { initGallery } from "./modules/gallery.js";
import { initStepper } from "./modules/stepper.js";
import { initMenuFilters } from "./modules/filter.js";

document.addEventListener("DOMContentLoaded", () => {
  initBurger();
  initGallery();
  initStepper();
  initMenuFilters();
});
