<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget Monitoring</title>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f4f6;
    }
    .main-content {
        transition: margin-left 0.3s;
    }
    .card {
        @apply bg-white p-6 rounded-lg shadow-md transition-transform transform hover:scale-105;
    }
    .bg-utilization {
        @apply bg-blue-100 text-blue-800;
    }
    .bg-spending {
        @apply bg-green-100 text-green-800;
    }
    .bg-alerts {
        @apply bg-red-100 text-red-800;
    }
    .modal-backdrop-custom {
      background-color: rgba(0, 0, 0, 0.5) !important;
    }
  </style>
</head>
<body>

<?php include 'layout/sidebar.php'; ?>
<script src="layout/resources/js/sidebar.js"></script>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-all">
     
     <div class="pb-5 border-b border-gray-200 animate-fadeIn">
       <h1 class="text-3xl font-bold bg-white bg-clip-text text-[#191970]">📊 Budget Monitoring Dashboard</h1>
     </div>
  
    <section class="p-5">
         <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="p-5 text-white rounded-lg shadow-md bg-blue-400">
        <i class='bx  bx-credit-card-alt'  ></i> 
      <h4 id="utilization-percentage"  class="text-xl font-medium">Budget Utilization</h4>
    </div>
    <div class="p-5 text-white rounded-lg shadow-md bg-green-500">
      <h4 class="text-xl font-medium">Over/Under-spending</h4>
      <p id="spending-status" class="text-lg"></p>
    </div>
    <div class="p-5 text-white rounded-lg shadow-md bg-red-500">
      <h4 class="text-xl font-medium">Alerts</h4>
      <p id="alerts-count" class="text-lg">2 Departments Near Limit</p>
    </div>
  </div>
        <div class="container mx-auto mt-8 bg-white p-6 rounded-lg shadow-xl">
            <h3 class="text-2xl font-semibold mb-6 text-gray-800">Department Budgets</h3>

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                <div class="w-full md:w-1/2">
                    <input type="text" id="searchInput" placeholder="Search by department or fiscal year..." class="w-full p-3 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out shadow-sm">
                </div>
                </div>

            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiscal Year</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody id="budgetTableBody" class="bg-white divide-y divide-gray-200">
                        <tr id="loadingRow">
                            <td colspan="8" class="text-center py-4 text-gray-500">Loading budgets...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="container mx-auto">
        <br>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Planned vs Actual Expenses</h2>
                <canvas id="lineChart"></canvas>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Spending by Departments</h2>
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <script >
// Data for the charts
const expensesData = [
    { month: 'Jan', amount: 350, category: 'Food' },
    { month: 'Feb', amount: 420, category: 'Rent' },
    { month: 'Mar', amount: 380, category: 'Utilities' },
    { month: 'Apr', amount: 500, category: 'Food' },
    { month: 'May', amount: 450, category: 'Entertainment' },
    { month: 'Jun', amount: 600, category: 'Rent' },
    { month: 'Jul', amount: 480, category: 'Food' },
    { month: 'Aug', amount: 550, category: 'Shopping' },
    { month: 'Sep', amount: 520, category: 'Utilities' },
    { month: 'Oct', amount: 490, category: 'Food' },
    { month: 'Nov', amount: 650, category: 'Rent' },
    { month: 'Dec', amount: 700, category: 'Entertainment' }
];

const departmentSpendingData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
        {
            label: 'HR - Planned ($)',
            data: [1500, 1500, 1500, 1500, 1500, 1500, 1500, 1500, 1500, 1500, 1500, 1500],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        },
        {
            label: 'HR - Actual ($)',
            data: [1450, 1520, 1480, 1600, 1550, 1580, 1510, 1590, 1620, 1550, 1650, 1700],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        },
        {
            label: 'LOGISTIC - Planned ($)',
            data: [2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000],
            borderColor: 'rgb(255, 159, 64)',
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        },
        {
            label: 'LOGISTIC - Actual ($)',
            data: [1900, 2050, 1950, 2100, 2000, 2150, 2080, 2120, 2050, 2100, 2180, 2250],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        },
        {
            label: 'CORE TRANSACTION - Planned ($)',
            data: [1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000],
            borderColor: 'rgb(153, 102, 255)',
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        },
        {
            label: 'CORE TRANSACTION - Actual ($)',
            data: [950, 1020, 980, 1050, 1000, 1080, 1020, 1090, 1050, 1100, 1150, 1200],
            borderColor: 'rgb(255, 205, 86)',
            backgroundColor: 'rgba(255, 205, 86, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        },
    ]
};

// --- Line Chart Logic (Combined Data) ---
const labels = departmentSpendingData.labels;
const totalPlannedData = Array(labels.length).fill(0);
const totalActualData = Array(labels.length).fill(0);

departmentSpendingData.datasets.forEach(dataset => {
    dataset.data.forEach((value, index) => {
        if (dataset.label.includes('Planned')) {
            totalPlannedData[index] += value;
        } else if (dataset.label.includes('Actual')) {
            totalActualData[index] += value;
        }
    });
});

const lineCtx = document.getElementById('lineChart').getContext('2d');
new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Planned Spending ($)',
            data: totalPlannedData,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        }, {
            label: 'Total Actual Spending ($)',
            data: totalActualData,
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            title: {
                display: true,
                text: 'Total Planned vs. Actual Spending'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Amount ($)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Month'
                }
            }
        }
    }
});

// --- Pie Chart Logic ---
const categories = {};
expensesData.forEach(item => {
    categories[item.category] = (categories[item.category] || 0) + item.amount;
});

const categoryLabels = Object.keys(categories);
const categoryData = Object.values(categories);

const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: categoryLabels,
        datasets: [{
            label: 'Spending by Category',
            data: categoryData,
            backgroundColor: [
                'rgb(255, 99, 132)',  // Red
                'rgb(54, 162, 235)',  // Blue
                'rgb(255, 205, 86)',  // Yellow
                'rgb(75, 192, 192)',  // Green
                'rgb(153, 102, 255)', // Purple
                'rgb(255, 159, 64)',  // Orange
            ],
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        const total = categoryData.reduce((acc, current) => acc + current, 0);
                        const value = tooltipItem.raw;
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${tooltipItem.label}: $${value} (${percentage}%)`;
                    }
                }
            },
            title: {
                display: false
            }
        }
    }
});

;</script>
    </section>

    <div id="detailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center modal-backdrop-custom">
        <div class="relative p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-2xl font-semibold text-gray-900">Budget Details</h3>
                <button class="text-gray-400 hover:text-gray-600 text-3xl font-bold" onclick="closeModal('detailsModal')">&times;</button>
            </div>
            <div class="mt-4 text-gray-700 space-y-3" id="modalContent">
                </div>
            <div class="mt-4 flex justify-end">
                <button onclick="generateReport()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-150">Generate Report</button>
            </div>
        </div>
    </div>

</main>

<script src="layout/resources/js/sub_module_3_budget_monitoring.js"></script>

</body>
</html>