export function initStepper() {
  const steps = document.querySelectorAll(".commande-step");
  const stepperItems = document.querySelectorAll(".stepper__item");
  const aside = document.querySelector(".commande-aside");
  const formContainer = document.querySelector(".commande-page__container");

  const btnNext = document.getElementById("btn-next");
  const btnPrev = document.getElementById("btn-prev");
  const btnSubmit = document.getElementById("btn-submit");

  let currentStep = 0;

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
