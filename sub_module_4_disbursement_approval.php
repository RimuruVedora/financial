<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* Custom CSS for the modal blur effect to ensure cross-browser compatibility */
    .modal-backdrop-blur {
      background-color: rgba(107, 114, 128, 0.5);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }
  </style>
</head>
<body class="bg-base-100">

<?php include 'layout/ap_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
  <div class="pb-5 border-b border-base-300 animate-fadeIn">
    <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
  </div>
  <section class="p-5">
  <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Disbursement Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
    <p class="text-sm font-semibold text-gray-500 uppercase">Total Disbursement</p>
    <p id="total-disbursement" class="text-4xl font-bold text-blue-600 mt-2">₱0</p>
</div>
<div class="bg-white rounded-lg shadow-md p-6">
    <p class="text-sm font-semibold text-gray-500 uppercase">Completed Transactions</p>
    <p id="completed-count" class="text-4xl font-bold text-green-600 mt-2">0</p>
</div>
<div class="bg-white rounded-lg shadow-md p-6">
    <p class="text-sm font-semibold text-gray-500 uppercase">Pending</p>
    <p id="pending-count" class="text-4xl font-bold text-yellow-600 mt-2">0</p>
</div>


        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Transaction Details</h2>

            <div role="tablist" class="tabs tabs-boxed mb-4">
                <a role="tab" class="tab tab-active" onclick="showTab('employees-table')">Employee</a>
                <a role="tab" class="tab" onclick="showTab('vendors-table')">Supplier</a>
            </div>

            <div id="employees-table" class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3 border-b-2 border-gray-200">Name</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Ticket Entry</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Contact</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Payment Method</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Due Date</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employees-table-body">
                       
                    </tbody>
                </table>
            </div>

            <div id="vendors-table" class="overflow-x-auto hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3 border-b-2 border-gray-200">Vendor Name</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Ticket Entry</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Contact</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Payment Method</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Payment Due</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendors-table-body">

                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden modal-backdrop-blur">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Confirm Disbursement Details</h3>
                <button onclick="closeModal('confirmModal')" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
            <form id="disbursementForm" class="space-y-4">
            <input type="hidden" id="modalTicketEntry" name="ticket_Entry">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="payable-type" class="block text-sm font-medium text-gray-700">Payable Type</label>
                    <select id="payable-type" name="payable-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option>Vendor Payment</option>
                        <option>Salary</option>
                        <option>Reimbursement</option>
                    </select>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" id="amount" name="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="requested-amount" class="block text-sm font-medium text-gray-700">Requested Amount</label>
                    <input type="number" id="requested-amount" name="requested-amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="payment-method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select id="payment-method" name="payment-method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option>Bank Transfer</option>
                        <option>Credit Card</option>
                        <option>Cash</option>
                    </select>
                </div>
            </form>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="closeModal('confirmModal')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button onclick="openFinalConfirmation()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <div id="finalModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden modal-backdrop-blur">
        <div class="relative top-20 mx-auto p-5 border w-80 shadow-lg rounded-md bg-white">
            <div class="text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Are you sure?</h3>
                <p class="text-sm text-gray-500">Do you want to proceed with this disbursement?</p>
            </div>
            <div class="mt-4 flex justify-center gap-4">
                <button onclick="closeModal('finalModal')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    No
                </button>
                <button onclick="submitDisbursement()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Yes
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openFinalConfirmation() {
            closeModal('confirmModal');
            openModal('finalModal');
        }

        function submitDisbursement() {
            // This is where you would handle the actual form submission logic
            // For now, we'll just close the modal and show a simple alert.
            console.log("Disbursement confirmed and submitted!");
            closeModal('finalModal');
            alert('Transaction successfully approved!');
            // Here you would typically send data to a server and update the UI
        }
        
        function showTab(tabId) {
          // Hide all tables
          document.getElementById('employees-table').classList.add('hidden');
          document.getElementById('vendors-table').classList.add('hidden');
          
          // Show the selected table
          document.getElementById(tabId).classList.remove('hidden');
          
          // Update active tab class
          const tabs = document.querySelectorAll('.tabs a');
          tabs.forEach(tab => tab.classList.remove('tab-active'));
          event.target.classList.add('tab-active');
        }
    </script>
  </section>
</main>
<script src="layout/resources/js/sidebar.js"></script>
<script src="layout/resources/js/sub_module_4_disbursement.js"></script>
</body>
</html>