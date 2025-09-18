const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');
const filterButton = document.getElementById('filterButton');
const filterOptions = document.getElementById('filterOptions');
const reportTableBody = document.getElementById('reportTableBody');
const loadingOverlay = document.getElementById('loadingOverlay');
const modalOverlay = document.getElementById('modalOverlay');
const modalContent = document.getElementById('modalContent');
const closeModalButton = document.getElementById('closeModal');
const dateFromInput = document.getElementById('dateFrom');
const dateToInput = document.getElementById('dateTo');

let data = [];

// Fetch data from the PHP backend
async function fetchData() {
    loadingOverlay.style.display = 'flex';
    try {
        const response = await fetch('php/sub_module_3_budget_Report.php?action=fetch');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();
        if (result.success) {
            data = result.data;
            renderTable(data);
        } else {
            displayMessage(`Error: ${result.message}`);
        }
    } catch (error) {
        displayMessage('Failed to fetch data. Please try again later.');
        console.error('Fetch error:', error);
    } finally {
        loadingOverlay.style.display = 'none';
    }
}

// Render the main table with data
function renderTable(dataToRender) {
    reportTableBody.innerHTML = '';
    if (dataToRender.length === 0) {
        reportTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">No reports found.</td></tr>';
        return;
    }
    dataToRender.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.report_ticket}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.department_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.date_time}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="showDetailsModal(${item.department_id})" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">View Details</button>
            </td>
        `;
        reportTableBody.appendChild(row);
    });
}

// Show the modal with detailed report information
async function showDetailsModal(departmentId) {
    modalContent.innerHTML = 'Loading...';
    modalOverlay.style.display = 'flex';
    loadingOverlay.style.display = 'flex';

    try {
        const response = await fetch(`php/sub_module_3_budget_Report.php?action=getDetails&department_id=${departmentId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();
        if (result.success && result.data) {
            renderModalContent(result.data);
        } else {
            modalContent.innerHTML = `<p class="text-red-500">${result.message || 'Failed to fetch details.'}</p>`;
        }
    } catch (error) {
        modalContent.innerHTML = '<p class="text-red-500">An error occurred while fetching data. Please try again later.</p>';
        console.error('Fetch error:', error);
    } finally {
        loadingOverlay.style.display = 'none';
    }
}

// Render the modal content
function renderModalContent(details) {
    let overageCause = details.overageFlag ? details.overageCause : null;

    // Separate the overage-causing employee/vendor from the rest
    let overageEmployee = null;
    let otherEmployees = [];
    if (details.employees && overageCause) {
        overageEmployee = details.employees.find(e => e.Payable_Type === overageCause && e.Payable_Status === 'APPROVED');
        otherEmployees = details.employees.filter(e => !(e.Payable_Type === overageCause && e.Payable_Status === 'APPROVED'));
    } else {
        otherEmployees = details.employees;
    }

    let overageVendor = null;
    let otherVendors = [];
    if (details.vendors && overageCause) {
        overageVendor = details.vendors.find(v => v.Payable_Type === overageCause && v.Payable_Status === 'APPROVED');
        otherVendors = details.vendors.filter(v => !(v.Payable_Type === overageCause && v.Payable_Status === 'APPROVED'));
    } else {
        otherVendors = details.vendors;
    }

    // Build the HTML, placing the overage cause first
    let employeesHtml = '';
    if (overageEmployee) {
        employeesHtml += `<li class="p-2 border-b border-gray-200 bg-red-200">
            <p><strong>Employee:</strong> ${overageEmployee.employee_name}</p>
            <p><strong>Payable Type:</strong> ${overageEmployee.Payable_Type}</p>
            <p><strong>Amount:</strong> ₱${parseFloat(overageEmployee.Requested_Amount).toFixed(2)}</p>
            <p><strong>Status:</strong> <span class="badge ${getStatusColor(overageEmployee.Payable_Status)}">${overageEmployee.Payable_Status}</span></p>
        </li>`;
    }
    employeesHtml += otherEmployees.map(e => `
        <li class="p-2 border-b border-gray-200">
            <p><strong>Employee:</strong> ${e.employee_name}</p>
            <p><strong>Payable Type:</strong> ${e.Payable_Type}</p>
            <p><strong>Amount:</strong> ₱${parseFloat(e.Requested_Amount).toFixed(2)}</p>
            <p><strong>Status:</strong> <span class="badge ${getStatusColor(e.Payable_Status)}">${e.Payable_Status}</span></p>
        </li>
    `).join('');
    if (details.employees.length === 0) {
        employeesHtml = '<li class="text-gray-500">No approved employee payables.</li>';
    }

    let vendorsHtml = '';
    if (overageVendor) {
        vendorsHtml += `<li class="p-2 border-b border-gray-200 bg-red-200">
            <p><strong>Vendor:</strong> ${overageVendor.Company_Name}</p>
            <p><strong>Payable Type:</strong> ${overageVendor.Payable_Type}</p>
            <p><strong>Amount:</strong> ₱${parseFloat(overageVendor.Request_Amount).toFixed(2)}</p>
            <p><strong>Status:</strong> <span class="badge ${getStatusColor(overageVendor.Payable_Status)}">${overageVendor.Payable_Status}</span></p>
        </li>`;
    }
    vendorsHtml += otherVendors.map(v => `
        <li class="p-2 border-b border-gray-200">
            <p><strong>Vendor:</strong> ${v.Company_Name}</p>
            <p><strong>Payable Type:</strong> ${v.Payable_Type}</p>
            <p><strong>Amount:</strong> ₱${parseFloat(v.Request_Amount).toFixed(2)}</p>
            <p><strong>Status:</strong> <span class="badge ${getStatusColor(v.Payable_Status)}">${v.Payable_Status}</span></p>
        </li>
    `).join('');
    if (details.vendors.length === 0) {
        vendorsHtml = '<li class="text-gray-500">No approved vendor payables.</li>';
    }

    let overageInfo = '';
    if (details.overageFlag) {
        overageInfo = `
            <div class="bg-red-100 p-4 rounded-lg mb-4 border border-red-300">
                <p class="font-bold text-red-700">Overage Detected!</p>
                <p>Overage Percentage: <span class="font-bold">${details.overagePercentage}%</span></p>
                <p>Primary Cause: <span class="font-bold">${details.overageCause}</span></p>
                <p>Contribution to Total Spending: <span class="font-bold">${details.overageCausePercentage}%</span></p>
            </div>
        `;
    }

    modalContent.innerHTML = `
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Department: ${details.department_name}</h3>
        
        ${overageInfo}
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h4 class="font-medium text-gray-700">Allocated Budget</h4>
                <p class="text-2xl font-bold text-green-600">₱${parseFloat(details.allocatedAmount).toFixed(2)}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h4 class="font-medium text-gray-700">Contingency Fund</h4>
                <p class="text-2xl font-bold text-blue-600">₱${parseFloat(details.contingencyFund).toFixed(2)}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h4 class="font-medium text-gray-700">Total Spent</h4>
                <p class="text-2xl font-bold ₱${getOverageColor(details.overageFlag)}">₱${parseFloat(details.totalSpent).toFixed(2)}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h4 class="font-medium text-gray-700">Total Upcoming</h4>
                <p class="text-2xl font-bold text-yellow-600">₱${parseFloat(details.totalUpcoming).toFixed(2)}</p>
            </div>
        </div>

        <div class="mb-6">
            <h4 class="font-medium text-gray-700 mb-2">Approved Employee Payables</h4>
            <ul class="list-disc list-inside bg-white rounded-lg p-4 shadow-sm max-h-48 overflow-y-auto">
                ${employeesHtml}
            </ul>
        </div>

        <div>
            <h4 class="font-medium text-gray-700 mb-2">Approved Vendor Payables</h4>
            <ul class="list-disc list-inside bg-white rounded-lg p-4 shadow-sm max-h-48 overflow-y-auto">
                ${vendorsHtml}
            </ul>
        </div>
    `;
}

// Search and filter logic
function handleSearch() {
    const searchTerm = searchInput.value.toLowerCase();
    const dateFrom = dateFromInput.value;
    const dateTo = dateToInput.value;
    const filteredData = data.filter(item => {
        const matchesSearch = searchTerm === '' ||
            item.report_ticket.toString().includes(searchTerm) ||
            item.department_name.toLowerCase().includes(searchTerm);
        
        const matchesDate = (dateFrom === '' || new Date(item.date_time) >= new Date(dateFrom)) &&
                            (dateTo === '' || new Date(item.date_time) <= new Date(dateTo));
        
        return matchesSearch && matchesDate;
    });
    renderTable(filteredData);
}

// Helper function to get status color
function getStatusColor(status) {
    switch (status.toUpperCase()) {
        case 'APPROVED': return 'bg-green-500';
        case 'REJECTED': return 'bg-red-500';
        case 'PENDING':
        default: return 'bg-yellow-500';
    }
}

// Helper function to get overage color
function getOverageColor(flag) {
    return flag ? 'text-red-600' : 'text-green-600';
}

// Hide the modal
function hideModal() {
    modalOverlay.style.display = 'none';
}

// Simple message display
function displayMessage(message) {
    const existingMessage = document.getElementById('messageBox');
    if (existingMessage) existingMessage.remove();

    const messageBox = document.createElement('div');
    messageBox.id = 'messageBox';
    messageBox.textContent = message;
    messageBox.className = 'fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50';
    document.body.appendChild(messageBox);
    setTimeout(() => {
        messageBox.remove();
    }, 5000);
}

// Event listeners
searchButton.addEventListener('click', handleSearch);
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        handleSearch();
    }
});
filterButton.addEventListener('click', () => {
    filterOptions.classList.toggle('hidden');
});
closeModalButton.addEventListener('click', hideModal);
window.addEventListener('load', fetchData);