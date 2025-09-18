document.addEventListener("DOMContentLoaded", () => {
    // Get references
    const dailyCard = document.getElementById("dailyDisbursement");
    const weeklyCard = document.getElementById("weeklyDisbursement");
    const monthlyCard = document.getElementById("monthlyDisbursement");
    const totalCard = document.getElementById("totalDisbursement");
  
    const departmentChartCanvas = document.getElementById("departmentChart");
    const categoryChartCanvas = document.getElementById("categoryChart");
  
    let departmentChart = null;
    let categoryChart = null;
  
    async function fetchDashboard() {
      const response = await fetch("php/sub_module_4_analytic_report.php");
      return await response.json();
    }
  
    function updateCards(cards) {
      if (dailyCard) dailyCard.textContent = `₱ ${cards.daily.toLocaleString()}`;
      if (weeklyCard) weeklyCard.textContent = `₱ ${cards.weekly.toLocaleString()}`;
      if (monthlyCard) monthlyCard.textContent = `₱ ${cards.monthly.toLocaleString()}`;
      if (totalCard) totalCard.textContent = `₱ ${cards.total.toLocaleString()}`;
    }
  
    function renderDepartmentChart(chartData) {
      if (departmentChart) departmentChart.destroy();
      departmentChart = new Chart(departmentChartCanvas, {
        type: "bar",
        data: {
          labels: chartData.labels,
          datasets: [
            {
              label: "Disbursed Amount",
              data: chartData.data,
              backgroundColor: "rgba(54, 162, 235, 0.6)",
              borderColor: "rgba(54, 162, 235, 1)",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => "₱ " + value.toLocaleString(),
              },
            },
          },
        },
      });
    }
  
    function renderCategoryChart(chartData) {
      if (categoryChart) categoryChart.destroy();
      categoryChart = new Chart(categoryChartCanvas, {
        type: "pie",
        data: {
          labels: chartData.labels,
          datasets: [
            {
              data: chartData.data,
              backgroundColor: [
                "rgba(255, 99, 132, 0.6)",
                "rgba(54, 162, 235, 0.6)",
                "rgba(255, 206, 86, 0.6)",
                "rgba(75, 192, 192, 0.6)",
                "rgba(153, 102, 255, 0.6)",
                "rgba(255, 159, 64, 0.6)",
              ],
              borderColor: "#fff",
              borderWidth: 1,
            },
          ],
        },
        options: { responsive: true },
      });
    }
  
    async function loadDashboard() {
      try {
        const data = await fetchDashboard();
        updateCards(data.cards);
        renderDepartmentChart(data.department_chart);
        renderCategoryChart(data.category_pie);
      } catch (error) {
        console.error("Error loading dashboard:", error);
      }
    }
  
    // Initial load
    loadDashboard();
  });
  