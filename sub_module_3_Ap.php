<?php
// Your existing PHP code for includes, if any.
// The main backend logic has been moved to a separate file.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payable Dashboard</title>
    <link rel="stylesheet" href="layout/resources/css/payable_table.css">
    <link rel="stylesheet" href="layout/resources/css/payable_modal.css">
    <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<style>
    .modal-backdrop {
    z-index: 40; /* Lower than the PDF modal, but higher than the main content */
}
.modal-container {
    z-index: 50; /* A value that ensures it sits above the backdrop */
}
.modal-backdrop-blur {
    background-color: rgba(0, 0, 0, 0.5); /* This is the semi-transparent background */
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
</style>
<body>

<?php include 'layout/sidebar.php'; ?>
<script src="layout/resources/js/sidebar.js"></script>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow mx-auto w-full max-w-6xl">
    <header class="flex flex-col md:flex-row justify-between items-center pb-5 border-b border-gray-300 animate-fadeIn">
        <h1 id="dashboard-title" class="text-2xl font-bold text-[#191970]">Payable Dashboard: Employee Only</h1>
    </header>

    <section class="p-5">
        <div class="flex justify-center items-center">
            <div class="w-full max-w-4xl p-6 bg-white rounded-2xl shadow-xl border border-gray-200">
                <div class="tab-buttons">
                    <button class="tab-button active" onclick="showTab('employee', this)">Employee</button>
                    <button class="tab-button" onclick="showTab('vendor', this)">Supplier</button>
                </div>

                <div id="employee-tab" class="tab-content active">
                    <div class="overflow-x-auto shadow-lg rounded-xl">
                 
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-yellow-100 p-4 rounded-xl shadow-md text-center">
        <h3 class="text-lg font-semibold text-yellow-800">total Payable</h3>
        <p class="text-2xl font-bold text-yellow-900 mt-2" id="total-payable">
            <span class="flex items-end justify-center gap-1">
                <i class='bx bx-coin text-3xl text-yellow-900'></i>₱12,000
            </span>
        </p>
    </div>
    <div class="bg-orange-200 p-4 rounded-xl shadow-md text-center">
        <h3 class="text-lg font-semibold text-orange-800">Employee's Payable Cost</h3>
        <p class="text-2xl font-bold text-orange-900 mt-2" id="employee-cost">
            <span class="flex items-end justify-center gap-1">
            <i class='bx bx-money text-3xl text-orange-900'></i>

        </span>
        </p>
    </div>
    <div class="bg-amber-300 p-4 rounded-xl shadow-md text-center">
        <h3 class="text-lg font-semibold text-amber-800">Total Requests</h3>
        <p class="text-2xl font-bold text-amber-900 mt-2" id="employee-requests">
            <span class="flex items-end justify-center gap-1">
                <i class='bx bx-file-blank text-3xl text-amber-900'></i>25
            </span>
        </p>
    </div>
</div>
<header class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                        <div class="relative w-full sm:w-1/2">
                            <input type="text" id="employee-search" onkeyup="filterTable('employee')" placeholder="Search..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <button onclick="showFilterModal('employee')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold hover:bg-gray-300 transition-colors duration-200">
                            filter
                        </button>
                    </header>
                        <table class="table w-full">
                            <thead>
                                <tr class="bg-primary text-primary-content">
                                    <th>No#</th>
                                    <th>Name</th>
                                    <th>Requested Amount</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="records-table-body-employee">
                             
                            </tbody>
                        </table>
                    </div>
                    <div id="no-records-message-employee" class="text-center mt-8 text-gray-500 hidden">
                        <p>No records found. Please add a new record to get started.</p>
                    </div>
                </div>

                <div id="vendor-tab" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-100 p-4 rounded-xl shadow-md text-center">
            <h3 class="text-lg font-semibold text-yellow-800">total Payable</h3>
            <p class="text-2xl font-bold text-yellow-900 mt-2" id="vendor-total-payable">
                <span class="flex items-end justify-center gap-1">
                    <i class='bx bx-coin text-3xl text-yellow-900'></i>₱12,000
                </span>
            </p>
        </div>
        
    <div class="bg-orange-200 p-4 rounded-xl shadow-md text-center">
        <h3 class="text-lg font-semibold text-orange-800">Supplier's Payable Cost</h3>
        <p class="text-2xl font-bold text-orange-900 mt-2" id="vendor-cost">
            <span class="flex items-end justify-center gap-1">
                <i class='bx bx-money text-3xl text-orange-900'></i>
            </span>
        </p>
    </div>
    <div class="bg-amber-300 p-4 rounded-xl shadow-md text-center">
        <h3 class="text-lg font-semibold text-amber-800">Total Requests</h3>
        <p class="text-2xl font-bold text-amber-900 mt-2" id="vendor-requests">
            <span class="flex items-end justify-center gap-1">
                <i class='bx bx-file-blank text-3xl text-amber-900'></i>
            </span>
        </p>
    </div>
</div>
             
                           <header class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                        <div class="relative w-full sm:w-1/2">
                            <input type="text" id="vendor-search" onkeyup="filterTable('vendor')" placeholder="Search..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <button onclick="showFilterModal('vendor')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold hover:bg-gray-300 transition-colors duration-200">
                            filter
                        </button>
                    </header>
                    <div class="overflow-x-auto shadow-lg rounded-xl">
                        <table class="table w-full">
                            <thead>
                                <tr class="bg-primary text-primary-content">
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Requested Amount</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="records-table-body-vendor">
                                </tbody>
                        </table>
                    </div>
                    <div id="no-records-message-vendor" class="text-center mt-8 text-gray-500 hidden">
                        <p>No records found. Please add a new record to get started.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<div id="employee-modal" class="modal-backdrop">
    <div class="modal-container max-h-screen overflow-y-auto">
        <button class="modal-close-btn" onclick="hideModal('employee')">&times;</button>
        <div class="modal-content">
            <div class="modal-header flex items-center justify-between">
                <img src="layout/resources/images/sample.png" alt="Employee Image" class="modal-image">
                <div class="flex flex-col gap-2">
                    <span id="employee-priority-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500 text-white">
                        High
                    </span>
                    <span id="employee-status-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
                        allocated status:
                    </span>
                </div>
            </div>
            <div class="modal-content-details">
                <p><strong>Employee Name:</strong> <span id="employee-name"></span></p>
                <p><strong>Position:</strong> <span id="employee-position"></span></p>
                <p><strong>Department:</strong> <span id="employee-department"></span></p>
                <p><strong>Age:</strong> <span id="employee-age"></span></p>
                <p><strong>Contact number:</strong> <span id="employee-contact"></span></p>
                <p><strong>Email:</strong> <span id="employee-email"></span></p>
                <p><strong>Gender:</strong> <span id="employee-gender"></span></p>
                <p><strong>Amount Requested:</strong> <span id="employee-amount"></span></p>
                <p><strong>Due date:</strong> <span id="employee-due-date"></span></p>
                <p><strong>Payment Method:</strong> <span id="employee-payment-method"></span></p>
                <p><strong>Status:</strong> <span id="employee-status"></span></p>
            </div>
            <div class="pdf-uploaded-btn" onmouseover="changeText(this, 'Click to Download')" onmouseout="changeText(this, 'pdf uploaded')">
                <span id="employee-pdf-btn" onclick="showPdfModal()">pdf uploaded</span>
            </div>
            <div class="modal-actions">
    <button data-action="approve" class="bg-green-500 text-white rounded-xl px-4 py-2 font-semibold hover:bg-green-600 transition-colors duration-200">
        Approve
    </button>
    <button data-action="reject" class="bg-red-500 text-white rounded-xl px-4 py-2 font-semibold hover:bg-red-600 transition-colors duration-200">
        Reject
    </button>
            </div>
        </div>
    </div>
</div>
<div id="vendor-modal" class="modal-backdrop">
    <div class="modal-container max-h-screen overflow-y-auto">
        <button class="modal-close-btn" onclick="hideModal('vendor')">&times;</button>
        <div class="modal-content">
            <div class="modal-header">
                <img src="" alt="Vendor Image" class="modal-image">
                <div class="flex flex-col gap-2">
                    <span id="vendor-priority-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500 text-white">
                        High
                    </span>
                    <span id="vendor-status-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
                        allocated status:
                    </span>
                </div>
            </div>
            <div class="modal-content-details">
                <p><strong>Vendor Name:</strong> <span id="vendor-name"></span></p>
                <p><strong>Company Name:</strong> <span id="vendor-company"></span></p>
                <p><strong>Cost Center:</strong> <span id="vendor-cost-center"></span></p>
                <p><strong>Contact number:</strong> <span id="vendor-contact"></span></p>
                <p><strong>Email:</strong> <span id="vendor-email"></span></p>
                <p><strong>Company Address:</strong> <span id="vendor-address"></span></p>
                <p><strong>Amount Requested:</strong> <span id="vendor-amount"></span></p>
                <p><strong>Timestamp:</strong> <span id="vendor-timestamp"></span></p>
                <p><strong>Due date:</strong> <span id="vendor-due-date"></span></p>
                <p><strong>Payment Method:</strong> <span id="vendor-payment-method"></span></p>
                <p><strong>Status:</strong> <span id="vendor-status"></span></p>
                <p><strong>Purpose:</strong> <span id="vendor-purpose"></span></p>
            </div>
            <div class="pdf-uploaded-btn" onmouseover="changeText(this, 'Click to Download')" onmouseout="changeText(this, 'pdf uploaded')">
                <span id="vendor-pdf-btn" onclick="showPdfModal()">pdf uploaded</span>
            </div>
            <div class="modal-actions">
            <button data-action="approve" class="bg-green-500 text-white rounded-xl px-4 py-2 font-semibold hover:bg-green-600 transition-colors duration-200">
        Approve
    </button>
    <button data-action="reject" class="bg-red-500 text-white rounded-xl px-4 py-2 font-semibold hover:bg-red-600 transition-colors duration-200">
        Reject
    </button>
            </div>
        </div>
    </div>
</div>

<div class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center" id="viewStatementModal" aria-labelledby="viewStatementModalLabel" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-50">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="viewStatementModalLabel">View Statement</h3>
                    <div class="mt-4" style="height: 70vh;">
                        <iframe id="pdfViewer" src="" frameborder="0" class="w-full h-full"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="hidePdfModal()">Close</button>
        </div>
    </div>
</div>
<div id="filter-modal" class="modal-backdrop hidden">
    <div class="modal-container">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Filter Options</h2>
            <div class="flex flex-col gap-4">
                <div>
                    <label for="date-filter" class="block text-sm font-medium text-gray-700">Sort by Date</label>
                    <select id="date-filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-400 focus:border-gray-400 sm:text-sm rounded-md">
                        <option value="">Select Option</option>
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
                <div>
                    <label for="time-filter" class="block text-sm font-medium text-gray-700">Sort by Time</label>
                    <select id="time-filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-400 focus:border-gray-400 sm:text-sm rounded-md">
                        <option value="">Select Option</option>
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
                <div id="company-filter-container" class="hidden">
                    <label for="company-filter" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <select id="company-filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-400 focus:border-gray-400 sm:text-sm rounded-md">
                        <option value="">Select Company</option>
                        <option value="Tech Solutions Inc.">Tech Solutions Inc.</option>
                        <option value="Global Innovations">Global Innovations</option>
                        </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button onclick="hideFilterModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-colors duration-200">
                    Close
                </button>
                <button onclick="applyFilter()" class="px-4 py-2 bg-[#191970] text-white rounded-xl font-semibold hover:bg-[#191970]/90 transition-colors duration-200">
                    Apply Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div id="ticketModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-backdrop-blur">
    <div class="bg-white p-6 rounded-lg shadow-xl relative z-10 w-full max-w-sm mx-auto">
        <h3 class="text-xl font-bold mb-4" id="ticketModalTitle"></h3>
        <p class="text-gray-700 mb-2">Ticket Entry:</p>
        <p class="text-lg font-semibold text-[#191970] mb-4" id="ticketEntry"></p>
        <p class="text-green-600 font-medium" id="ticketMessage"></p>
        <div class="mt-4 flex justify-end">
            <button onclick="closeTicketModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-backdrop-blur">
    <div class="bg-white p-6 rounded-lg shadow-xl relative z-10 w-full max-w-sm mx-auto">
        <h3 class="text-xl font-bold mb-4 text-red-600">Reject Ticket</h3>
        <p class="text-gray-700 mb-2">Please provide a reason for rejection:</p>
        <textarea id="rejectReasonInput" class="w-full h-24 p-2 border rounded-lg text-sm" placeholder="Enter reason here..."></textarea>
        <div class="mt-4 flex justify-end gap-2">
            <button onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-colors duration-200">
                Cancel
            </button>
            <button id="confirmRejectBtn" class="px-4 py-2 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors duration-200">
                Confirm Reject
            </button>
        </div>
    </div>
</div>
<script src="layout/resources/js/payable.js"></script>

</body>
</html>