<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Replica</title>
    <link rel="stylesheet" href="layout/resources/css/account_Receivable_modal.css">
    <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #f3f4f6;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            position: relative;
            border: 2px solid black;
        }
        .modal-content-sm {
            background-color: #f3f4f6;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 350px;
            position: relative;
            border: 2px solid black;
            text-align: center;
        }
        .close-button {
            position: absolute;
            top: 8px;
            right: 12px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: black;
        }
        /* Prefixed modal styles to avoid DaisyUI conflicts */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999 !important;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            pointer-events: auto;
        }

        .modal-backdrop.is-visible {
            display: flex;
        }

        .modal-container, .modal-container-sm {
            background-color: #f3f4f6;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            position: relative;
            border: 2px solid black;
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .modal-container-sm { max-width: 350px; text-align:center; }
        .close-button { position:absolute; top:8px; right:12px; font-size:24px; font-weight:bold; cursor:pointer; color:black; }
    </style>
</head>
<body>
<?php include 'layout/sidebar.php'; ?>
<script src="layout/resources/js/sidebar.js"></script>
<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow ">

    <div class="pb-5 border-b border-base-300 animate-fadeIn">
        <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
    </div>

    <div class="w-full max-w-4xl p-6 bg-white rounded-xl shadow-lg border border-gray-300">

        <div class="flex items-center justify-between mb-6">
            <div class="relative w-1/3">
                <input type="text" placeholder="Search" class="w-full px-4 pl-10 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <button class="px-6 py-2 bg-gray-300 text-black rounded-full text-sm font-medium hover:bg-gray-400 transition-colors -ml-40">all</button>

            <div class="flex items-center space-x-2 bg-gray-200 rounded-full px-4 py-2">
                <div class="flex items-center justify-center h-8 w-8 bg-black rounded-full text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-black font-semibold">700</span>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-4 mb-4 text-gray-700 font-semibold text-center">
            <div class="col-span-1">Name</div>
            <div class="col-span-1">Ticket Entry</div>
            <div class="col-span-1">Balance</div>
            <div class="col-span-1">Due Date</div>
            <div class="col-span-1">Actions</div>
        </div>

        <div class="space-y-4" id="table-rows-container">
            </div>
    </div>
    <div id="myModal" class="modal-backdrop" aria-hidden="true">
        <div class="modal-container">
            <span class="close-button close-modal1" role="button" aria-label="Close">&times;</span>
            <div class="bg-gray-900 text-white font-semibold rounded-lg px-4 py-2 text-center mb-6" style="background-color: #21253c; border: 2px solid black;">
                ticket entry: <span id="modal-ticket-entry"></span>
            </div>
            <div class="space-y-4 text-black">
                <div><span class="font-semibold">Payor's name:</span> <span id="modal-payor-name"></span></div>
                <div><span class="font-semibold">Refund Amount:</span> <span id="modal-paid-amount"></span></div>
                <div><span class="font-semibold">Balance:</span> <span id="modal-balance"></span></div>
                <div><span class="font-semibold">remittance for:</span> <span id="modal-remittance-for"></span></div>
                <div><span class="font-semibold">date:</span> <span id="modal-date"></span></div>
                <div><span class="font-semibold">Payment Method:</span> <span id="modal-payment-method"></span></div>
            </div>
            <button id="modal-confirm-button" class="w-full px-4 py-2 bg-red-400 text-black rounded-lg text-sm font-medium hover:bg-orange-500 transition-colors mt-6">Confirm Refund</button>
        </div>
    </div>

    <div id="confirmationModal" class="modal-backdrop" aria-hidden="true">
        <div class="modal-container-sm">
            <span class="close-button close-modal2" role="button" aria-label="Close">&times;</span>
            <div class="text-center my-6 text-black">
                Are you sure to confirm this <br>Account Receivables
            </div>
            <div class="flex justify-center space-x-4">
                <button id="yes-button" class="px-6 py-2 bg-green-400 text-black rounded-lg font-medium hover:bg-green-500 transition-colors">Yes</button>
                <button id="no-button" class="px-6 py-2 bg-red-500 text-black rounded-lg font-medium hover:bg-red-600 transition-colors">No</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableRowsContainer = document.getElementById('table-rows-container');
            const myModal = document.getElementById("myModal");
            const confirmationModal = document.getElementById("confirmationModal");
            const closeButton1 = document.querySelector(".close-modal1");
            const closeButton2 = document.querySelector(".close-modal2");
            const modalConfirmButton = document.getElementById("modal-confirm-button");
            const yesButton = document.getElementById("yes-button");
            const noButton = document.getElementById("no-button");
            
            // Modal data display elements
            const modalTicketEntry = document.getElementById('modal-ticket-entry');
            const modalPayorName = document.getElementById('modal-payor-name');
            const modalPaidAmount = document.getElementById('modal-paid-amount');
            const modalBalance = document.getElementById('modal-balance');
            const modalRemittanceFor = document.getElementById('modal-remittance-for');
            const modalDate = document.getElementById('modal-date');
            const modalPaymentMethod = document.getElementById('modal-payment-method');

            let currentTicketData = {}; // Global object to hold data for the current ticket

            function openModal(el) {
                el.classList.add('is-visible');
                el.setAttribute('aria-hidden', 'false');
            }

            function closeModal(el) {
                el.classList.remove('is-visible');
                el.setAttribute('aria-hidden', 'true');
            }

            // Function to fetch and display data
            async function fetchAccountReceivables() {
                try {
                    const response = await fetch('php/account_Receivable_Refund_Data_Fetcher.php');
                    const data = await response.json();

                    tableRowsContainer.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(item => {
                            const row = document.createElement('div');
                            row.className = 'grid grid-cols-5 gap-4 bg-gray-200 p-4 rounded-lg items-center text-gray-700 text-center';
                            row.innerHTML = `
                                <div class="col-span-1">${item.name}</div>
                                <div class="col-span-1">${item.ticket_Entry}</div>
                                <div class="col-span-1">${item.balance}</div>
                                <div class="col-span-1">${item.date}</div>
                                <div class="col-span-1 flex justify-end">
                                    <button class="w-full px-4 py-2 bg-green-400 text-black rounded-lg text-sm font-medium hover:bg-green-500 transition-colors confirm-button"
                                            data-ticket-id="${item.TICKET_ID}"
                                            data-ticket-entry="${item.ticket_Entry}"
                                            data-name="${item.name}"
                                            data-amount="${item.amount}"
                                            data-balance="${item.balance}"
                                            data-remittance-for="${item.remittance_for}"
                                            data-date="${item.date}"
                                            data-payment-method="${item.type}">Confirm</button>
                                </div>
                            `;
                            tableRowsContainer.appendChild(row);
                        });
                    } else {
                        tableRowsContainer.innerHTML = '<div class="text-center py-8 text-gray-500">No accepted accounts receivables found.</div>';
                    }

                } catch (error) {
                    console.error('Error fetching data:', error);
                    tableRowsContainer.innerHTML = '<div class="text-center py-8 text-red-500">Failed to load data. Please try again later.</div>';
                }
            }

            // Call the function when the page loads
            fetchAccountReceivables();

            // Event delegation for dynamically created buttons
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('confirm-button')) {
                    // Store data from the clicked button
                    currentTicketData = {
                        ticket_id: event.target.dataset.ticketId,
                        ticket_entry: event.target.dataset.ticketEntry,
                        name: event.target.dataset.name,
                        amount: event.target.dataset.amount,
                        balance: event.target.dataset.balance,
                        remittance_for: event.target.dataset.remittanceFor,
                        date: event.target.dataset.date,
                        payment_method: event.target.dataset.paymentMethod
                    };
                    
                    // Populate the modal with the stored data
                    modalTicketEntry.textContent = currentTicketData.ticket_entry;
                    modalPayorName.textContent = currentTicketData.name;
                    modalPaidAmount.textContent = currentTicketData.amount;
                    modalBalance.textContent = currentTicketData.balance;
                    modalRemittanceFor.textContent = currentTicketData.remittance_for;
                    modalDate.textContent = currentTicketData.date;
                    modalPaymentMethod.textContent = currentTicketData.payment_method;

                    openModal(myModal);
                }
            });

            modalConfirmButton.addEventListener('click', function() {
                closeModal(myModal);
                openModal(confirmationModal);
            });

            yesButton.addEventListener('click', async function() {
                closeModal(confirmationModal);
                
                // Call the new backend to confirm the receivable and add to revenue
                try {
                    const response = await fetch('php/account_Receivable_Confirm_Refund.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            ticket_id: currentTicketData.ticket_id,
                            amount: currentTicketData.amount
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Account receivable confirmed and revenue recorded successfully!');
                        // Re-fetch data to update the table
                        fetchAccountReceivables();
                    } else {
                        alert('Failed to confirm account receivable. ' + result.message);
                    }
                    
                } catch (error) {
                    console.error('Error confirming receivable:', error);
                    alert('An error occurred. Please try again.');
                }
            });

            noButton.addEventListener('click', function() {
                closeModal(confirmationModal);
            });

            closeButton1.addEventListener('click', function() {
                closeModal(myModal);
            });

            closeButton2.addEventListener('click', function() {
                closeModal(confirmationModal);
            });

            document.addEventListener('click', function(e) {
                if (e.target === myModal) closeModal(myModal);
                if (e.target === confirmationModal) closeModal(confirmationModal);
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal(myModal);
                    closeModal(confirmationModal);
                }
            });
        });
    </script>
</main>
</body>
</html>