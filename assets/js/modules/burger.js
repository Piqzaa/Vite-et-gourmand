export function initBurger() {
  const burger = document.querySelector(".navbar__burger");
  const mobileMenu = document.querySelector(".navbar__mobile");

  if (!burger || !mobileMenu) return;

  burger.addEventListener("click", () => {
    const isOpen = mobileMenu.classList.toggle("is-open");
    burger.classList.toggle("is-open");
    burger.setAttribute("aria-expanded", isOpen);
    mobileMenu.setAttribute("aria-hidden", !isOpen);
  });
}
