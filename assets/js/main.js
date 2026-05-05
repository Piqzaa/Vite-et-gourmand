import { initBurger } from "./modules/burger.js";
import { initGallery } from "./modules/gallery.js";
import { initStepper } from "./modules/stepper.js";
import { initMenuFilters } from "./modules/filter.js";
import { initProfil } from "./modules/profil.js";
import { initEmployeFilters } from "./modules/employe.js";
import { initAdminChart } from "./modules/admin-chart.js";
import { initResetPassword } from "./modules/reset-password.js";

document.addEventListener("DOMContentLoaded", () => {
  initBurger();
  initGallery();
  initStepper();
  initProfil();
  initMenuFilters();
  initEmployeFilters();
  initAdminChart();
  initResetPassword();
});
