export function initGallery() {
  const mainImg = document.querySelector("#gallery-main-img");
  const thumbs = document.querySelectorAll(".menu-gallery__thumb");

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      mainImg.src = thumb.src;

      thumbs.forEach((t) => t.classList.remove("menu-gallery__thumb--active"));

      thumb.classList.add("menu-gallery__thumb--active");
    });
  });
}
