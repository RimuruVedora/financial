  // URL for the backend API
  const API_URL = 'php/sub_module_3_monitoring_backend.php';

  document.addEventListener('DOMContentLoaded', () => {
      fetchBudgets();
      fetchDashboardMetrics();
      document.getElementById('searchInput').addEventListener('keyup', filterTable);
  });

  // Fetches dashboard metrics from the backend
  async function fetchDashboardMetrics() {
      try {
          const response = await fetch(`${API_URL}?action=get_dashboard_metrics`);
          const result = await response.json();

          if (result.success) {
              const data = result.data;
              document.getElementById('utilization-percentage').textContent = `${(data.utilization_percentage * 100).toFixed(0)}% Utilized`;
              document.getElementById('spending-status').textContent = data.over_under_spending > 0 ? `Over by ₱${formatAmount(Math.abs(data.over_under_spending))}` : `Under by ₱${formatAmount(Math.abs(data.over_under_spending))}`;
              document.getElementById('alerts-count').textContent = `${data.alerts_count} Departments Near Limit`;
          } else {
              console.error('Error fetching dashboard metrics:', result.error);
          }
      } catch (error) {
          console.error('Fetch error:', error);
      }
  }

  // Fetches budget data from the backend
  async function fetchBudgets() {
      const tableBody = document.getElementById('budgetTableBody');
      tableBody.innerHTML = '<tr id="loadingRow"><td colspan="8" class="text-center py-4 text-gray-500">Loading budgets...</td></tr>';

      try {
          const response = await fetch(`${API_URL}?action=get_department_budgets`);
          const result = await response.json();
          
          if (result.success) {
              renderBudgets(result.data);
          } else {
              tableBody.innerHTML = `<tr class="text-center text-red-500"><td colspan="8" class="py-4">Error: ${result.error}</td></tr>`;
          }
      } catch (error) {
          tableBody.innerHTML = `<tr class="text-center text-red-500"><td colspan="8" class="py-4">Error fetching data. Please check the network.</td></tr>`;
          console.error('Fetch error:', error);
      }
  }

  // Renders the budget data into the table
  function renderBudgets(budgets) {
      const tableBody = document.getElementById('budgetTableBody');
      tableBody.innerHTML = ''; // Clear loading row

      if (budgets.length === 0) {
          tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-gray-500">No budget data available.</td></tr>`;
          return;
      }

      budgets.forEach(budget => {
          const status = getStatus(budget.allocated_amount, budget.spent_amount);
          const statusClass = getStatusClass(status);
          const row = document.createElement('tr');
          row.className = 'hover:bg-gray-50 transition-colors duration-150';
          row.innerHTML = `
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${budget.department}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${budget.fiscal_year}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱${formatAmount(budget.allocated_amount)}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱${formatAmount(budget.spent_amount)}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-bold ${getRemainingColor(budget.remaining_amount)}">
                  ₱${formatAmount(budget.remaining_amount)}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                      ${status}
                  </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${budget.date_time}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button onclick="showDetails(${budget.department_budget_id})" class="text-blue-600 hover:text-blue-900 transition-colors">Details</button>
              </td>
          `;
          tableBody.appendChild(row);
      });
  }

  // Filters the table based on search input
  function filterTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toUpperCase();
      const tableBody = document.getElementById('budgetTableBody');
      const rows = tableBody.getElementsByTagName('tr');

      for (let i = 0; i < rows.length; i++) {
          const departmentCell = rows[i].getElementsByTagName('td')[0];
          const yearCell = rows[i].getElementsByTagName('td')[1];
          if (departmentCell || yearCell) {
              const departmentText = departmentCell.textContent || departmentCell.innerText;
              const yearText = yearCell.textContent || yearCell.innerText;
              if (departmentText.toUpperCase().indexOf(filter) > -1 || yearText.toUpperCase().indexOf(filter) > -1) {
                  rows[i].style.display = "";
              } else {
                  rows[i].style.display = "none";
              }
          }
      }
  }
  
  // Shows the budget details modal
  async function showDetails(id) {
      const modal = document.getElementById('detailsModal');
      const modalContent = document.getElementById('modalContent');
      // Set the budget ID as a data attribute on the modal for easy access
      modal.setAttribute('data-budget-id', id);
      modalContent.innerHTML = '<div class="text-center py-4 text-gray-500">Loading details...</div>';

      try {
          const response = await fetch(`${API_URL}?action=get_budget_details&id=${id}`);
          const result = await response.json();

          if (result.success) {
              const data = result.data;
              const remaining = data.allocated_amount - data.spent_amount;
              
              modalContent.innerHTML = `
                  <p><strong>Department:</strong> <span id="modalDepartment">${data.department}</span></p>
                  <p><strong>Fiscal Year:</strong> <span id="modalFiscalYear">${data.fiscal_year}</span></p>
                  <p><strong>Justification:</strong> <span id="modalJustification">${data.justification}</span></p>
                  <p><strong>Allocated:</strong> ₱<span id="modalAllocatedAmount">${formatAmount(data.allocated_amount)}</span></p>
                  <p><strong>Spent:</strong> ₱<span id="modalSpentAmount">${formatAmount(data.spent_amount)}</span></p>
                  <p><strong>Remaining:</strong> ₱<span id="modalRemainingAmount" class="${getRemainingColor(remaining)}">${formatAmount(remaining)}</span></p>
              `;
          } else {
              modalContent.innerHTML = `<div class="text-center py-4 text-red-500">${result.error}</div>`;
          }
      } catch (error) {
          modalContent.innerHTML = `<div class="text-center py-4 text-red-500">Error fetching details.</div>`;
          console.error('Details fetch error:', error);
      }

      modal.classList.remove('hidden');
  }

  // Closes a modal
  function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
  }

  // Helper function to determine budget status
  function getStatus(allocated, spent) {
      const utilization = (spent / allocated) * 100;
      if (utilization >= 100) {
          return 'Overused';
      } else if (utilization >= 90) {
          return 'Caution';
      } else {
          return 'Healthy';
      }
  }

  // Helper function to get status class for styling
  function getStatusClass(status) {
      switch (status) {
          case 'Healthy':
              return 'bg-green-100 text-green-800';
          case 'Caution':
              return 'bg-yellow-100 text-yellow-800';
          case 'Overused':
              return 'bg-red-100 text-red-800';
          default:
              return 'bg-gray-100 text-gray-800';
      }
  }
  // Helper function to format amount with commas
  function formatAmount(amount) {
      return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
  }

  // Helper function to get color based on remaining amount
  function getRemainingColor(amount) {
      return amount < 0 ? 'text-red-500' : 'text-green-600';
  }

  // New function to handle report generation
  async function generateReport() {
      const modal = document.getElementById('detailsModal');
      const departmentBudgetId = modal.getAttribute('data-budget-id');

      if (!departmentBudgetId) {
          alert("Error: Budget ID not found.");
          return;
      }

      if (!confirm("Are you sure you want to generate a report for this budget?")) {
          return;
      }

      try {
          const response = await fetch(`${API_URL}?action=generate_report`, {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `id=${departmentBudgetId}`
          });
          const result = await response.json();

          if (result.success) {
              alert(`Report generated successfully! Ticket ID: ${result.data.report_ticket}`);
              closeModal('detailsModal');
          } else {
              alert(`Failed to generate report: ${result.error}`);
          }
      } catch (error) {
          console.error('Report generation error:', error);
          alert('An error occurred while generating the report.');
      }
  }