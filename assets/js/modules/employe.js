export function initEmployeFilters() {
  const filtreStatut = document.getElementById("filtre-statut");
  if (!filtreStatut) return;

  const filtreClient = document.getElementById("filtre-client");
  const cards = document.querySelectorAll(".employe-commandes .commande-card");

  function filtrer() {
    const statut = filtreStatut.value.toLowerCase();
    const client = filtreClient.value.toLowerCase().trim();

    cards.forEach((card) => {
      const cardStatut = card.dataset.statut?.toLowerCase() ?? "";
      const cardClient = card.dataset.client?.toLowerCase() ?? "";

      const matchStatut = !statut || cardStatut === statut;
      const matchClient = !client || cardClient.includes(client);

      card.style.display = matchStatut && matchClient ? "" : "none";
    });
  }

  filtreStatut.addEventListener("change", filtrer);
  filtreClient.addEventListener("input", filtrer);
}
