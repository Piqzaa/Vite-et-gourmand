export function initResetPassword() {
  const loginView = document.getElementById("login-view");
  const forgotView = document.getElementById("forgot-view");
  const btnShowForgot = document.getElementById("btn-show-forgot");
  const btnShowLogin = document.getElementById("btn-show-login");

  if (!loginView || !forgotView || !btnShowForgot || !btnShowLogin) {
    return;
  }

  // Afficher Form Oublié
  btnShowForgot.addEventListener("click", function (e) {
    e.preventDefault();
    loginView.style.display = "none";
    forgotView.style.display = "block";
  });

  // Afficher Form Connexion
  btnShowLogin.addEventListener("click", function (e) {
    e.preventDefault();
    forgotView.style.display = "none";
    loginView.style.display = "block";
  });

  if (window.location.hash === "#forgot") {
    loginView.style.display = "none";
    forgotView.style.display = "block";
  }
}
