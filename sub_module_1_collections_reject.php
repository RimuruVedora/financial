<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="layout/resources/css/collection_reject.css">
  <link rel="stylesheet" href="layout/resources/css/collection_reject_modal.css">
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<?php include 'layout/ar_sidebar.php'; 
 /* lagay or mention nyo to lagi sa mga 
files nyo eto kasi yung sidebar same din 
 sa script since dapat magksama yan lagi 
 yung script sya yung nag addd ng functionality
 sa sidebar nyo
*/

?>
<script src="layout/resources/js/sidebar.js"></script>
<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow ">
     
     <div class="pb-5 border-b border-base-300 animate-fadeIn">
       <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
     </div>
  

       <section class="p-5">

       <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="flex flex-col gap-4">
                <div class="total-card total-update">
                    <span class="icon">&#9993;</span>
                    <span>total update <span id="updatedCount"></span></span>
                </div>
                <div class="total-card total-rejected">
                    <span class="icon">&#9993;</span>
                    <span>TO FOLLOW  <span id="rejectedCount"></span></span>
                </div>
            </div>
            <div class="col-span-2">
                <div class="incoming-revenue-card">
                    Incoming Revenue <span class="p-currency ml-2" id="incomingRevenue"></span>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
            <div class="search-container flex-grow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                <input type="text" placeholder="Search..." class="flex-grow ml-2" id="searchInput">
            </div>
            <div class="dropdown">
                <button class="all-button" id="allButton">
                    all <span class="ml-2">&#x25BC;</span>
                </button>
                <div class="dropdown-menu" id="allDropdown">
                    <a href="#" data-filter="all">All</a>
                    <a href="#" data-filter="updated">Updated</a>
                    <a href="#" data-filter="rejected">Rejected</a>
                </div>
            </div>
        </div>

        <div class="table-row-container">
            <div class="table-row font-bold header-bg rounded-t-lg">
                <div class="text-left">Ticket Entry:</div>
                <div class="text-left">Name</div>
                <div class="text-left">Revenue</div>
                <div class="text-left">Status</div>
                <div class="text-center">Action</div>
            </div>

            <div id="tableBody"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let initialData = [];
            let currentData = [];

            const tableBody = document.getElementById('tableBody');
            const searchInput = document.getElementById('searchInput');
            const allButton = document.getElementById('allButton');
            const allDropdown = document.getElementById('allDropdown');
            const updatedCountSpan = document.getElementById('updatedCount');
            const rejectedCountSpan = document.getElementById('rejectedCount');
            const incomingRevenueSpan = document.getElementById('incomingRevenue');

            const viewModal = document.getElementById('viewModal');
            const modalTicketEntry = document.getElementById('ticketEntry');
            const modalName = document.getElementById('modalName');
            const modalRevenue = document.getElementById('modalRevenue');
            const modalType = document.getElementById('modalType');
            const modalDate = document.getElementById('modalDate');
            const modalResubmittedDate = document.getElementById('modalResubmittedDate');
            const modalTime = document.getElementById('modalTime');
            const modalCloseButton = viewModal.querySelector('.modal-close-button');
            const modalPdfButton = viewModal.querySelector('.modal-pdf-button');
            const modalApproveButton = viewModal.querySelector('.modal-approve-button');
            const modalRejectButton = viewModal.querySelector('.modal-reject-button');

            // Function to fetch data from the backend
            async function fetchData() {
                try {
                    const response = await fetch('php/Collection_Reject_ Data_Fetcher.php');
                    const result = await response.json();
                    if (result.error) {
                        console.error("Backend Error:", result.error);
                        return { total_revenue: '₱ 0.00', data: [] };
                    }
                    return result;
                } catch (error) {
                    console.error("Failed to fetch data:", error);
                    return { total_revenue: '₱ 0.00', data: [] };
                }
            }

            // Function to update the counts on the dashboard
            function updateCounts() {
                const updatedCount = initialData.filter(item => item.action === 'updated').length;
                const rejectedCount = initialData.filter(item => item.action === 'reject').length;

                updatedCountSpan.textContent = updatedCount;
                rejectedCountSpan.textContent = rejectedCount;
            }

            // Function to render the table with the provided data
            function renderTable(items) {
                tableBody.innerHTML = '';
                items.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'table-row';
                    row.innerHTML = `
                        <div>${item.ticket_entry}</div>
                        <div>${item.name}</div>
                        <div>${item.revenue}</div>
                        <div>
                            <span class="status-badge ${item.action}">
                                ${item.action}
                            </span>
                        </div>
                        <div class="text-center">
                            <button class="status-button" 
                                data-ticket-id="${item.ticket_id}"
                                data-collection-id="${item.collection_id}"
                                data-ticket-entry="${item.ticket_entry}" 
                                data-name="${item.name}" 
                                data-revenue="${item.revenue}" 
                                data-type="${item.type}" 
                                data-date="${item.date}" 
                                data-resubmitted-date="${item.resubmitted_date || ''}" 
                                data-time="${item.time}">
                                view
                            </button>
                        </div>
                    `;
                    tableBody.appendChild(row);
                });

                document.querySelectorAll('.status-button').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const { ticketId, collectionId, ticketEntry, name, revenue, type, date, resubmittedDate, time } = event.target.dataset;
                        
                        viewModal.dataset.ticketId = ticketId;
                        viewModal.dataset.collectionId = collectionId;
                        
                        modalTicketEntry.textContent = `ticket entry: ${ticketEntry}`;
                        modalName.textContent = name;
                        modalRevenue.textContent = revenue;
                        modalType.textContent = type;
                        modalDate.textContent = date;
                        modalResubmittedDate.textContent = resubmittedDate;
                        modalTime.textContent = time;
                        viewModal.classList.remove('hidden');
                    });
                });
            }

            // Function to filter and re-render the table
            function filterTable(filter) {
                if (filter === 'all') {
                    currentData = [...initialData];
                } else {
                    currentData = initialData.filter(item => item.action === filter);
                }
                renderTable(currentData);
            }

            // Function to handle approval and rejection actions
            async function handleAction(action) {
                const ticketId = viewModal.dataset.ticketId;
                const collectionId = viewModal.dataset.collectionId;

                if (!ticketId || !collectionId) {
                    alert('Error: Ticket or Collection ID is missing.');
                    return;
                }

                const formData = new FormData();
                formData.append('ticket_id', ticketId);
                formData.append('collection_id', collectionId);

                try {
                    const response = await fetch('php/collections_Reject_Action_handler.php', {
                        method: 'POST',
                        body: formData,
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        viewModal.classList.add('hidden');
                        const newResult = await fetchData();
                        incomingRevenueSpan.textContent = newResult.total_revenue;
                        initialData = newResult.data;
                        currentData = newResult.data;
                        renderTable(currentData);
                        updateCounts();
                    } else {
                        alert(`Error: ${result.error}`);
                    }
                } catch (error) {
                    console.error('Failed to perform action:', error);
                    alert('An error occurred. Please try again.');
                }
            }

            // Event listeners for the action buttons
            modalApproveButton.addEventListener('click', () => {
                handleAction('approve');
            });

            modalRejectButton.addEventListener('click', () => {
                alert('The reject functionality is not yet implemented on the backend.');
            });


            // Event listeners
            allButton.addEventListener('click', () => {
                allDropdown.style.display = allDropdown.style.display === 'block' ? 'none' : 'block';
            });

            allDropdown.addEventListener('click', (event) => {
                const filter = event.target.dataset.filter;
                if (filter) {
                    allButton.innerHTML = `${event.target.textContent} <span class="ml-2">&#x25BC;</span>`;
                    filterTable(filter);
                    allDropdown.style.display = 'none';
                }
            });
            
            document.addEventListener('click', (event) => {
                if (!allButton.contains(event.target) && !allDropdown.contains(event.target)) {
                    allDropdown.style.display = 'none';
                }
            });

            searchInput.addEventListener('input', (event) => {
                const searchTerm = event.target.value.toLowerCase();
                const filteredData = currentData.filter(item =>
                    item.name.toLowerCase().includes(searchTerm) ||
                    item.revenue.toLowerCase().includes(searchTerm) ||
                    item.type.toLowerCase().includes(searchTerm)
                );
                renderTable(filteredData);
            });

            // Event listener for closing the modal
            modalCloseButton.addEventListener('click', () => {
                viewModal.classList.add('hidden');
            });

            // Event listener to close modal if user clicks outside the modal content
            viewModal.addEventListener('click', (event) => {
                if (event.target === viewModal) {
                    viewModal.classList.add('hidden');
                }
            });

            // New hover functionality for PDF button
            modalPdfButton.addEventListener('mouseover', () => {
                modalPdfButton.textContent = 'click to download';
            });
            modalPdfButton.addEventListener('mouseout', () => {
                modalPdfButton.textContent = 'pdf';
            });
            
            // Initial data fetch and render
            fetchData().then(result => {
                incomingRevenueSpan.textContent = result.total_revenue;
                initialData = result.data;
                currentData = result.data;
                renderTable(currentData);
                updateCounts();
            });
        });
    </script>
     
       </section>   
 
   </main>

   <div id="viewModal" class="modal-backdrop hidden">
       <div class="modal-container">
           <div class="modal-content">
               <div class="modal-header">
                   <span id="ticketEntry">ticket entry: </span>
                   <span class="modal-close-button">&times;</span>
               </div>
               <br>
               <div class="modal-body">
                   <div class="modal-item">
                       <span class="modal-label">name:</span>
                       <span id="modalName"></span>
                   </div>
                   <div class="modal-item">
                       <span class="modal-label">amount:</span>
                       <span id="modalRevenue"></span>
                   </div>
                   <div class="modal-item">
                       <span class="modal-label">remittance for:</span>
                       <span id="modalType"></span>
                   </div>
                   <div class="modal-item">
                       <span class="modal-label">date:</span>
                       <span id="modalDate"></span>
                   </div>
                   <div class="modal-item">
                       <span class="modal-label">resubmitted date</span>
                       <span id="modalResubmittedDate"></span>
                   </div>
                   <div class="modal-item">
                       <span class="modal-label">time:</span>
                       <span id="modalTime"></span>
                   </div>
                   <div class="modal-item">
                       <span class="modal-label">Payment Method:</span>
                       <span id="modalFileType">pdf</span>
                   </div>
                   <div class="modal-file-button">
                       <button class="modal-pdf-button">pdf</button>
                   </div>
               </div>
               <div class="modal-footer">
                   <button class="modal-approve-button">approve</button>
                   <button class="modal-reject-button">reject</button>
               </div>
           </div>
       </div>
   </div>

</body>
</html>