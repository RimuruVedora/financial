<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejected Payable Tickets</title>
    <link rel="stylesheet" href="layout/resources/css/sidebar.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="layout/resources/css/sub_module_2_reject_payable.css">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .dashboard-card {
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            border-left: 4px solid #3b82f6;
        }
        .view-button {
            transition: all 0.3s ease;
        }
        .view-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-button-active {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }
        .modal-backdrop-blur {
            backdrop-filter: blur(5px);
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="bg-base-100">

<?php include 'layout/ap_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
    <div class="pb-5 border-b border-base-300 animate-fadeIn">
        <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Rejected Tickets Dashboard</h1>
    </div>
    <section class="p-5">
    <div class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-8">
      <div class="dashboard-card bg-white p-6 rounded-lg shadow-md animate-slideInLeft">
        <h3 class="text-lg font-bold text-gray-700 mb-2">Vendor Tickets</h3>
        <p class="text-3xl font-extrabold text-[#191970] mb-2"><span id="total-rejected">0</span></p>
        <p class="text-sm text-gray-500">Total Rejected Tickets</p>
      </div>
      <div class="dashboard-card bg-white p-6 rounded-lg shadow-md animate-slideInRight">
        <h3 class="text-lg font-bold text-gray-700 mb-2">Employee Tickets</h3>
        <p class="text-3xl font-extrabold text-[#191970] mb-2"><span id="employee-total-rejected">0</span></p>
        <p class="text-sm text-gray-500">Total Rejected Tickets</p>
      </div>
    </div>
    <div class="tabs tabs-boxed mb-6 bg-white shadow-md">
      <a id="vendor-tab" class="tab tab-active">
        <i class='bx bxs-user-pin text-xl mr-2'></i>
        supplier
      </a>
      <a id="employee-tab" class="tab">
        <i class='bx bxs-user text-xl mr-2'></i>
        Employee
      </a>
    </div>
    
    <div id="vendor-content" class="tab-content active">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Rejected Vendor Tickets</h2>
            <div class="flex justify-between items-center mb-4">
                <input type="text" id="vendor-searchInput" placeholder="Search by Ticket ID..." class="input input-bordered w-full max-w-xs transition-colors duration-200">
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Ticket ID</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Reason</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendor-tableBody" class="text-gray-600 text-sm font-light">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="employee-content" class="tab-content">
      <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Rejected Employee Tickets</h2>
            <div class="flex justify-between items-center mb-4">
                <input type="text" id="employee-searchInput" placeholder="Search by Ticket ID..." class="input input-bordered w-full max-w-xs transition-colors duration-200">
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Ticket ID</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Reason</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employee-tableBody" class="text-gray-600 text-sm font-light">
                        </tbody>
                </table>
            </div>
      </div>
    </div>
  </div>
    </section>
</main>

<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden modal-backdrop-blur">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6 animate-slideInUp">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal-title" class="text-xl font-semibold text-gray-800">Details</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <i class='bx bx-x text-2xl'></i>
            </button>
        </div>
        <div class="space-y-4">
            <div id="vendor-details" class="hidden">
                <p><span class="font-medium text-gray-600">Vendor Name:</span> <span id="modal-vendor-name"></span></p>
                <p><span class="font-medium text-gray-600">Company Name:</span> <span id="modal-company-name"></span></p>
                <p><span class="font-medium text-gray-600">Contact Number:</span> <span id="modal-contact-number"></span></p>
                <p><span class="font-medium text-gray-600">Email:</span> <span id="modal-vendor-email"></span></p>
                <p><span class="font-medium text-gray-600">Company Address:</span> <span id="modal-company-address"></span></p>
                <p><span class="font-medium text-gray-600">Amount Requested:</span> <span id="modal-vendor-amount-requested"></span></p>
                <p><span class="font-medium text-gray-600">Due Date:</span> <span id="modal-vendor-due-date"></span></p>
                <p><span class="font-medium text-gray-600">Payment Method:</span> <span id="modal-vendor-payment-method"></span></p>
                <p><span class="font-medium text-gray-600">Purpose:</span> <span id="modal-purpose"></span></p>
            </div>
            <div id="employee-details" class="hidden">
                <p><span class="font-medium text-gray-600">Employee Name:</span> <span id="modal-employee-name"></span></p>
                <p><span class="font-medium text-gray-600">Position:</span> <span id="modal-position"></span></p>
                <p><span class="font-medium text-gray-600">Department:</span> <span id="modal-department"></span></p>
                <p><span class="font-medium text-gray-600">Age:</span> <span id="modal-age"></span></p>
                <p><span class="font-medium text-gray-600">Gender:</span> <span id="modal-gender"></span></p>
                <p><span class="font-medium text-gray-600">Email:</span> <span id="modal-employee-email"></span></p>
                <p><span class="font-medium text-gray-600">Address:</span> <span id="modal-address"></span></p>
                <p><span class="font-medium text-gray-600">Amount Requested:</span> <span id="modal-employee-amount-requested"></span></p>
                <p><span class="font-medium text-gray-600">Due Date:</span> <span id="modal-employee-due-date"></span></p>
                <p><span class="font-medium text-gray-600">Payment Method:</span> <span id="modal-employee-payment-method"></span></p>
                <p><span class="font-medium text-gray-600">Justification:</span> <span id="modal-justification"></span></p>
            </div>
            <div class="pt-4 border-t">
                <p><span class="font-medium text-gray-600">Reason to Reject:</span> <span id="modal-reason-to-reject"></span></p>
            </div>
        </div>
    </div>
</div>
<script src="layout/resources/js/sub_module_2_reject_payable.js"></script>
<script src="layout/resources/js/sidebar.js"></script>
</body>
</html>