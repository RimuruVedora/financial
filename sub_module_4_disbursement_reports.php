<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disbursement Dashboard</title>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-base-100">

<?php include 'layout/ap_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
  <div class="pb-5 border-b border-base-300 animate-fadeIn">
    <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
  </div>

  <section class="p-5">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Disbursement Dashboard</h1>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-600">Total Recipients</h2>
                <p id="totalRecipients" class="text-4xl font-bold text-blue-500 mt-2">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-600">Total Suppliers</h2>
                <p id="totalSuppliers" class="text-4xl font-bold text-green-500 mt-2">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold text-gray-600">Total Employees</h2>
                <p id="totalEmployees" class="text-4xl font-bold text-purple-500 mt-2">0</p>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex border-b border-gray-200 mb-4">
                <button class="tab-button px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-500 border-b-2 border-transparent hover:border-blue-500 transition duration-300" data-tab="all">All</button>
                <button class="tab-button px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-500 border-b-2 border-transparent hover:border-blue-500 transition duration-300" data-tab="employee">Employees</button>
                <button class="tab-button px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-500 border-b-2 border-transparent hover:border-blue-500 transition duration-300" data-tab="vendor">Suppliers</button>
            </div>

            <!-- Search & Filter -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                <input type="text" id="searchInput" placeholder="Search by name..." class="flex-grow p-2 border border-gray-300 rounded-lg mb-2 md:mb-0 md:mr-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select id="filterSelect" class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Filter by Type</option>
                    <option value="employee">Employee</option>
                    <option value="vendor">Supplier</option>
                </select>
            </div>
            
            <!-- Table -->
            <div id="dashboardTable" class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Type</th>
                            <th class="py-3 px-6 text-left">Amount</th>
                            <th class="py-3 px-6 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody id="dataBody" class="text-gray-600 text-sm font-light"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let data = [];
        let activeTab = "all";

        const dataBody = document.getElementById('dataBody');
        const searchInput = document.getElementById('searchInput');
        const filterSelect = document.getElementById('filterSelect');
        const tabButtons = document.querySelectorAll('.tab-button');
        const totalRecipientsEl = document.getElementById('totalRecipients');
        const totalSuppliersEl = document.getElementById('totalSuppliers');
        const totalEmployeesEl = document.getElementById('totalEmployees');

        // Render rows in table
        const renderData = (records) => {
            dataBody.innerHTML = '';
            if (records.length === 0) {
                dataBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">No records found.</td></tr>';
                return;
            }
            records.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200 hover:bg-gray-50';
                row.innerHTML = `
                    <td class="py-3 px-6 text-left whitespace-nowrap">${item.name}</td>
                    <td class="py-3 px-6 text-left"><span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs">${item.type}</span></td>
                    <td class="py-3 px-6 text-left">₱ ${parseFloat(item.amount).toLocaleString()}</td>
                    <td class="py-3 px-6 text-left">${item.date_time}</td>
                `;
                dataBody.appendChild(row);
            });
        };

        // Update counts
        const updateSummaryCounts = () => {
            const totalEmployees = data.filter(item => item.type === 'Employee').length;
            const totalVendors = data.filter(item => item.type === 'Vendor').length;
            
            totalRecipientsEl.textContent = data.length;
            totalSuppliersEl.textContent = totalVendors;
            totalEmployeesEl.textContent = totalEmployees;
        };

        // Filter + search
        const filterAndSearch = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const filterType = filterSelect.value;

            let filteredData = data.filter(item => {
                const matchesTab = (activeTab === 'all' || item.type.toLowerCase() === activeTab);
                const matchesSearch = item.name.toLowerCase().includes(searchTerm);
                const matchesFilter = (filterType === 'all' || item.type.toLowerCase() === filterType);
                return matchesTab && matchesSearch && matchesFilter;
            });

            renderData(filteredData);
        };

        // Tab switching
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('active', 'border-blue-500'));
                button.classList.add('active', 'border-blue-500');
                activeTab = button.dataset.tab;
                filterAndSearch();
            });
        });

        searchInput.addEventListener('input', filterAndSearch);
        filterSelect.addEventListener('change', filterAndSearch);

        // Fetch data from backend
        fetch("php/sub_module_4_fetch_disbursement_reports.php")
    .then(res => res.json())
    .then(json => {
        if (json.success) {
            data = json.data;   // ✅ use the array inside
            updateSummaryCounts();
            tabButtons[0].classList.add('active', 'border-blue-500');
            renderData(data);
        } else {
            dataBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">No records found.</td></tr>';
        }
    })
    .catch(err => {
        console.error("Error fetching data:", err);
        dataBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-red-500">Failed to load data.</td></tr>';
    });

    });
    </script>
  </section>
</main>

<script src="layout/resources/js/sidebar.js"></script>
</body>
</html>
