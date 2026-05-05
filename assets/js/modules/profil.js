export function initProfil() {
  const pwdInput = document.getElementById("profil-password");
  if (!pwdInput) return;

  const confirmGroup = document.getElementById("confirm-password-group");
  const confirmInput = document.getElementById("profil-password-confirm");
  const profilForm = document.querySelector("#profil form");

  pwdInput.addEventListener("input", () => {
    confirmGroup.style.display = pwdInput.value ? "block" : "none";
    if (!pwdInput.value) confirmInput.value = "";
  });

  profilForm.addEventListener("submit", (e) => {
    if (!pwdInput.value) return;

    // Validation force mot de passe
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/;
    if (!passwordRegex.test(pwdInput.value)) {
      e.preventDefault();
      pwdInput.classList.add("form-input--error");
      pwdInput.focus();
      alert(
        "Mot de passe trop faible. Il doit contenir au minimum 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.",
      );
      return;
    }

    if (pwdInput.value !== confirmInput.value) {
      e.preventDefault();
      confirmInput.classList.add("form-input--error");
      confirmInput.focus();
      alert("Les mots de passe ne correspondent pas.");
      return;
    }

    if (!confirm("Voulez-vous vraiment changer votre mot de passe ?")) {
      e.preventDefault();
    }
  });
}
