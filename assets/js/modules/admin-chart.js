let myChart = null;
export function initAdminChart() {
  const canvas = document.getElementById("chart-commandes");
  if (!canvas) return;
  const btn = document.getElementById("btn-filtrer-stats");
  if (!btn) return;
  const initialData = JSON.parse(canvas.getAttribute("data-stats"));
  renderChart(canvas, initialData);

  btn.addEventListener("click", async () => {
    const menuId = document.getElementById("stats-menu").value;
    const debut = document.getElementById("stats-debut").value;
    const fin = document.getElementById("stats-fin").value;

    const response = await fetch(
      `assets/php/admin/get-stats.php?menu_id=${menuId}&date_debut=${debut}&date_fin=${fin}`,
    );
    const data = await response.json();

    document.getElementById("ca-total").textContent = new Intl.NumberFormat(
      "fr-FR",
      { style: "currency", currency: "EUR" },
    ).format(data.totals.ca || 0);
    document.getElementById("commandes-total").textContent =
      data.totals.nb || 0;
    document.getElementById("panier-moyen").textContent = new Intl.NumberFormat(
      "fr-FR",
      { style: "currency", currency: "EUR" },
    ).format(data.totals.moy || 0);

    updateChart(data.chart);
  });

  function renderChart(canvas, data) {
    myChart = new Chart(canvas, {
      type: "bar",
      data: {
        labels: data.labels,
        datasets: [
          {
            label: "Commandes (unités)",
            data: data.commandes,
            backgroundColor: "#c1440e",
            yAxisID: "y",
            maxBarThickness: 50,
          },
          {
            label: "CA (€)",
            data: data.ca,
            backgroundColor: "#e8a87c",
            yAxisID: "y1",
            maxBarThickness: 50,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          x: {
            grid: { display: false },
            offset: true,
          },
          y: {
            type: "linear",
            display: true,
            position: "left",
            title: { display: true, text: "Nombre de commandes" },
            beginAtZero: true,
          },
          y1: {
            type: "linear",
            display: true,
            position: "right",
            title: { display: true, text: "Chiffre d'affaires (€)" },
            beginAtZero: true,
            grid: { drawOnChartArea: false },
          },
        },
        plugins: {
          legend: { position: "top" },
        },
      },
    });
  }

  function updateChart(newData) {
    myChart.data.labels = newData.labels;
    myChart.data.datasets[0].data = newData.commandes;
    myChart.data.datasets[1].data = newData.ca;
    myChart.update();
  }
}
