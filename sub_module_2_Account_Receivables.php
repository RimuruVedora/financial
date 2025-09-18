<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Receivables</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

<?php include 'layout/ar_sidebar.php'; ?>
<script src="layout/resources/js/sidebar.js"></script>
<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow ">
     
     <div class="pb-5 border-b border-base-300 animate-fadeIn">
       <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
     </div>
  
       <section class="p-5">
       <div class="container mx-auto p-6 bg-white rounded-lg shadow-md mt-8 max-w-7xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Receivables & Aging Analysis</h2>

    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8" id="analysisTab" role="tablist">
        <a class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-indigo-600 border-indigo-500" id="receivables-tab" href="#receivables" role="tab" aria-controls="receivables" aria-selected="true">Receivables Table</a>
        <a class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="aging-tab" href="#aging" role="tab" aria-controls="aging" aria-selected="false">Aging by Bucket</a>
        <a class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="dso-tab" href="#dso" role="tab" aria-controls="dso" aria-selected="false">DSO Trend</a>
      </nav>
    </div>



    <div class="mt-6" id="analysisTabContent">

      <div class="tab-pane show active" id="receivables" role="tabpanel" aria-labelledby="receivables-tab">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-gray-50 p-6 rounded-lg shadow-sm text-center">
            <div class="text-sm font-semibold text-gray-600">Total Receivables</div>
            <div class="mt-1 text-xl font-bold text-gray-900" id="total-receivables">₱</div>
          </div>
          <div class="bg-gray-50 p-6 rounded-lg shadow-sm text-center">
            <div class="text-sm font-semibold text-gray-600">% Overdue</div>
            <div class="mt-1 text-xl font-bold text-gray-900" id="percent-overdue">0%</div>
          </div>
          <div class="bg-gray-50 p-6 rounded-lg shadow-sm text-center">
            <div class="text-sm font-semibold text-gray-600">Average DSO</div>
            <div class="mt-1 text-xl font-bold text-gray-900" id="average-dso">0 days</div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 shadow-sm rounded-lg">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Due</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aging Buckets</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payment Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collector Assigned</th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Action</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="receivables-table-body">
              </tbody>
          </table>
        </div>
      </div>

      <div class="hidden tab-pane" id="aging" role="tabpanel" aria-labelledby="aging-tab">
      <div style="height:400px;">
        <canvas id="agingChart" class="w-full"></canvas>
      </div>

    </div>

      <div class="hidden tab-pane" id="dso" role="tabpanel" aria-labelledby="dso-tab">
      <div style="height:400px;">
        <canvas id="dsoTrendChart" class="w-full"></canvas>
      </div>
</div>
    </div>
  </div>
    
  <div class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center" id="viewAgingDetailModal" aria-labelledby="viewAgingDetailModalLabel" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
  <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="viewAgingDetailModalLabel">View Aging Detail</h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500">Details of aging per invoice, payment notes, and escalation logs...</p>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-modal-hide="viewAgingDetailModal">Close</button>
      </div>
    </div>
  </div>

  <div class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center **bg-gray-900/50 backdrop-blur-sm**" id="sendReminderModal" aria-labelledby="sendReminderModalLabel" role="dialog" aria-modal="true">
  <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
      <div class="sm:flex sm:items-start">
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="sendReminderModalLabel">Assign Collector</h3>
          <div class="mt-4">
            <form id="assign-collector-form" class="space-y-4">
              <input type="hidden" id="ticket-id" name="ticketId">
              <div>
                <label for="collector-select" class="block text-sm font-medium text-gray-700">Available Collector</label>
                <select id="collector-select" name="collector" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                  </select>
              </div>
              <div>
                <label for="due-date" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" id="due-date" name="dueDate" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
  <button type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" id="save-assignment-btn" disabled>Save</button>
  <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm" data-modal-hide="sendReminderModal">Close</button>
</div>
  </div>
</div>

<div id="ticket-modal" class="modal-container fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-full max-w-sm relative">
            
            <div class="flex justify-between items-center mb-4">
                <h2 id="ticket-modal-title" class="text-xl font-semibold text-gray-800">ticket entry::10001</h2>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-700 transition" data-modal-hide="ticket-modal">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
</button>
            </div>
            
            <div class="space-y-4 text-gray-700">
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">name</span>
                    <span id="view-name" class="font-semibold text-gray-800">John Doe</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">paid amount</span>
                    <span id="view-paid-amount" class="font-semibold text-gray-800">$50.00</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">balance</span>
                    <span id="view-balance" class="font-semibold text-gray-800">$10.00</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">last paid date</span>
                    <span id="view-last-paid-date" class="font-semibold text-gray-800">2025-09-14</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">collector</span>
                    <span id="view-collector" class="font-semibold text-gray-800">Jane Smith</span>
                </div>
                 <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">notification status</span>
                    <span id="view-notification-status" class="font-semibold text-gray-800">N/A</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">claim status</span>
                    <span id="view-claim-status" class="font-semibold text-gray-800">N/A</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium text-gray-500 text-sm">aging bucket</span>
                    <span id="view-aging-bucket" class="font-semibold text-gray-800">0-30 days</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <button id="view-statement-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                data-modal-target="viewStatementModal" data-pdf-url="">View Statement</button>
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
        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-modal-hide="viewStatementModal">Close</button>
      </div>
    </div>
  </div>




  <div id="viewCollectorModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg transition-all transform scale-100 opacity-100" role="dialog" aria-modal="true" aria-labelledby="viewCollectorModalLabel">
            <div class="px-6 py-4">
                <h3 class="text-xl font-bold text-gray-900" id="viewCollectorModalLabel">Collector Assignment Details</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <p class="font-semibold text-gray-700">Notification Status:</p>
                        <p id="view-notification-status" class="text-gray-900"></p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700">Claim Status:</p>
                        <p id="view-claim-status" class="text-gray-900"></p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700">Notes:</p>
                        <p id="view-notes" class="text-gray-900"></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end p-4 bg-gray-50 rounded-b-lg">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-modal-hide="viewCollectorModal">
                    Close
                </button>
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

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="layout/resources/js/sub_module_2_receivable.js"></script>
</main>
</body>
</html>