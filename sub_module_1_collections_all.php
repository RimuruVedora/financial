<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="layout/resources/css/collection_modal.css">
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<?php include 'layout/ar_sidebar.php'; ?>
<script src="layout/resources/js/sidebar.js"></script>
<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
<div class="pb-5 border-b border-base-300 animate-fadeIn">
    <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
  </div>
  
  <section class="p-5 flex-1 flex flex-col">
    <div class="max-w-7xl mx-auto space-y-8">
      
        <div class="flex flex-col md:flex-row justify-center items-center gap-8">
            <div class="w-full md:w-1/2 p-6 bg-gray-200 border border-gray-400 rounded-3xl shadow-lg">
                <div class="flex flex-col items-center justify-center">
                    <span class="text-xl font-medium text-gray-700 mb-2">Total Receivable:</span>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span id="total-requests" class="text-3xl font-bold text-gray-800">0</span>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-1/2 p-6 bg-gray-200 border border-gray-400 rounded-3xl shadow-lg">
                <div class="flex flex-col items-center justify-center">
                    <span class="text-xl font-medium text-gray-700 mb-2">Incoming Revenue</span>
                    <span class="text-3xl font-bold text-gray-800" id="total-revenue">₱ 0.00</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-8">
            <div class="relative w-full md:w-1/2">
                <input type="text" id="search-input" placeholder=" " class="w-full pl-10 pr-4 py-2 border border-gray-400 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <button class="w-full md:w-auto px-6 py-2 bg-gray-300 border border-gray-500 rounded-full text-gray-800 font-medium hover:bg-gray-400 transition-colors">
                all
            </button>
        </div>
        <div class="w-full p-4 bg-gray-200 border border-gray-400 rounded-3xl shadow-lg mt-8 flex-1 flex flex-col overflow-hidden">
            <div class="grid grid-cols-5 md:grid-cols-5 gap-4 text-center font-bold text-gray-800 border-b-2 border-gray-400 pb-2 mb-2">
                <div class="p-2">Name</div>
                <div class="p-2">Revenue</div>
                <div class="p-2">Payment Method</div>
                <div class="p-2">Date</div>
                <div class="p-2">Action</div>
            </div>
            <div class="space-y-4 overflow-y-auto" id="table-body">
                </div>
        </div>
      </div>
     </section>
   </main>

    <div id="modal" class="modal-backdrop">
        <div class="modal-container">
            <button id="close-modal-btn" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="modal-content-details space-y-4 text-gray-800">
                <div class="text-lg font-semibold" id="modal-name">Name:</div>
                <div class="text-lg font-semibold" id="modal-or-no">OR No:</div>
                <div class="text-lg font-semibold" id="modal-location">Location:</div>
                <div class="text-lg font-semibold" id="modal-amount">Amount:</div>
                <div class="text-lg font-semibold" id="modal-remittance">Remittance for:</div>
                <div class="text-lg font-semibold" id="modal-date">Date:</div>
                <div class="text-lg font-semibold" id="modal-time">Time:</div>
                <div class="text-lg font-semibold" id="modal-type">Payment Method:</div>
            </div>
            <br>
            <div class="pdf-uploaded-btn" id="pdf-btn">
                <span>pdf uploaded</span>
            </div>
            <div class="modal-actions">
                <button id="approve-btn" class="approve-btn">
                    Accept
                </button>
                <button id="reject-btn" class="reject-btn">
                    Reject
                </button>
            </div>
        </div>
    </div>
    
    <div id="confirmation-modal" class="modal-backdrop">
        <div class="modal-container">
            <button id="close-confirmation-modal-btn" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="modal-content-details space-y-4 text-gray-800">
                <div class="text-lg font-semibold" id="confirmation-name">Name:</div>
                <div class="text-lg font-semibold" id="confirmation-amount">Amount:</div>
                <div class="text-lg font-semibold" id="confirmation-remittance">Remittance for:</div>
                <div class="text-lg font-semibold" id="confirmation-date">Date:</div>
                <div class="text-lg font-semibold" id="confirmation-time">Time:</div>
                <div class="text-lg font-semibold" id="confirmation-type">Type:</div>
            </div>
            <div class="mt-4 px-4 py-2 bg-slate-900 text-white font-medium rounded-lg text-center">
                Ticket Entry: <span id="ticket-entry"></span>
            </div>
            <div class="modal-actions mt-8">
                <button id="confirmation-accept-btn" class="approve-btn w-full">
                    Done
                </button>
            </div>
        </div>
    </div>

    <div id="alert-modal" class="modal-backdrop">
        <div class="modal-container">
            <div id="alert-message" class="text-center font-semibold text-gray-800"></div>
            <div class="modal-actions mt-4">
                <button id="close-alert-btn" class="approve-btn w-full">
                    OK
                </button>
            </div>
        </div>
    </div>
    
    <div id="reject-modal" class="modal-backdrop">
        <div class="modal-container">
            <button id="close-reject-modal-btn" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="modal-content-details space-y-4 text-gray-800">
                <div class="text-lg font-semibold">Reject Request</div>
                <div class="text-md font-medium" id="reject-modal-name">Name: </div>
                <div class="text-md font-medium" id="reject-modal-or-no">OR No: </div>
                <div class="text-md font-medium" id="reject-modal-location">Location: </div>
                <textarea id="rejection-reason" class="w-full p-2 border border-gray-400 rounded-md" rows="4" placeholder="Reason for rejection"></textarea>
            </div>
            <div class="modal-actions mt-4">
                <button id="confirm-reject-btn" class="reject-btn w-full">
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>

<script src="layout/resources/js/sub_module_1_collections_all.js"></script>
</body>
</html>