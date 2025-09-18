let agingChart, dsoTrendChart;

// This function fetches data and triggers rendering for the entire page
function fetchDataAndRender() {
    $.ajax({
        url: 'php/sub_module_2__Account_Receivable_backend.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            renderReceivablesTable(data);
            updateSummaryCards(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data:', textStatus, errorThrown);
        }
    });
    updateCharts();
}


// This function renders the main receivables table
function renderReceivablesTable(data) {
    const tableBody = $('#receivables-table-body');
    tableBody.empty();
    if (data.length === 0) {
        tableBody.append('<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No receivables data available.</td></tr>');
        return;
    }

    data.forEach(item => {
        // Use the correct column alias from the backend
        const collectorName = item.assigned_collector || 'N/A';

        const row = `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.customer_name || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱ ${item.total_due.toLocaleString()}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <p>Current: ₱ ${item.total_current_amount ? item.total_current_amount.toLocaleString() : 0}</p>
                    <p>30 Days: ₱ ${item.total_thirty_days ? item.total_thirty_days.toLocaleString() : 0}</p>
                    <p>60 Days: ₱ ${item.total_sixty_days ? item.total_sixty_days.toLocaleString() : 0}</p>
                    <p>90+ Days: ₱ ${item.total_ninety_days ? item.total_ninety_days.toLocaleString() : 0}</p>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.last_payment_date || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${collectorName}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button onclick="openAssignCollectorModal(${item.Ticket_ID})" class="text-indigo-600 hover:text-indigo-900">Assign</button>
                    <button onclick="openViewModal(${item.Ticket_ID})" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">View</button>
                </td>
            </tr>
        `;
        tableBody.append(row);
    });
}
// This function updates the summary cards with calculated data
// This function updates the summary cards with calculated data
function updateSummaryCards(data) {
    let totalReceivables = 0;
    let totalOverdue = 0;
    
    data.forEach(item => {
        // Ensure total_due is treated as a number for addition
        totalReceivables += parseFloat(item.total_due) || 0;
        
        // Ensure overdue amounts are treated as numbers
        if (item.thirty_days || item.sixty_days || item.ninety_days) {
            totalOverdue += (parseFloat(item.thirty_days) || 0) + (parseFloat(item.sixty_days) || 0) + (parseFloat(item.ninety_days) || 0);
        }
    });

    const percentOverdue = totalReceivables > 0 ? (totalOverdue / totalReceivables) * 100 : 0;
    const averageDSO = 45; // Placeholder for now, needs calculation logic

    $('#total-receivables').text(`₱ ${totalReceivables.toLocaleString()}`);
    $('#percent-overdue').text(`${percentOverdue.toFixed(2)}%`);
    $('#average-dso').text(`${averageDSO} days`);
}
// Fetches and updates the aging and DSO charts
// Fetches and updates the aging and DSO charts
function updateCharts() {
    // Fetch data for the aging chart
   // Fetch data for the aging chart
   fetch('php/sub_module_2__Account_Receivable_backend.php?fetch_aging_counts=true')
   .then(response => {
       if (!response.ok) throw new Error('Failed to fetch aging chart data.');
       return response.json();
   })
   .then(data => {
       const agingData = [
           data.current, 
           data.thirty_days, 
           data.sixty_days, 
           data.ninety_plus_days
       ];

       if (agingChart) {
           agingChart.destroy();
       }

       var ctx = document.getElementById('agingChart').getContext('2d');
       agingChart = new Chart(ctx, {
           type: 'pie',
           data: {
               labels: ['Current', '30 Days', '60 Days', '90+ Days'],
               datasets: [{
                   data: agingData,
                   backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384', '#4BC0C0'],
                   hoverOffset: 30 // This property makes the slice pop out on hover
               }]
           },
           options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                   legend: {
                       position: 'top',
                   },
                   title: {
                       display: true,
                       text: 'Number of Customers by Aging Bucket'
                   }
               }
           }
       });
   })
   .catch(error => {
       console.error('Error fetching aging chart data:', error);
   });


    // Fetch data for the DSO trend chart
    fetch('php/sub_module_2__Account_Receivable_backend.php?fetch_dso_trend=true')
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch DSO trend data.');
            return response.json();
        })
        .then(data => {
            const dsoLabels = data.map(item => item.month);
            const dsoValues = data.map(item => item.dso);

            if (dsoTrendChart) {
                dsoTrendChart.destroy();
            }

            var ctx2 = document.getElementById('dsoTrendChart').getContext('2d');
            dsoTrendChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: dsoLabels,
                    datasets: [{
                        label: 'DSO Trend',
                        data: dsoValues,
                        borderColor: '#FF6384',
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'DSO Trend Over the Last 6 Months'
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching DSO trend data:', error);
        });
}

// Function to handle the form submission
async function assignCollectorAndTicket() {
    const ticketId = document.getElementById('ticket-id').value;
    const collectorId = document.getElementById('collector-select').value;
    const dueDate = document.getElementById('due-date').value;

    if (!ticketId || !collectorId || !dueDate) {
        showErrorModal("Please fill all fields.");
        return;
    }

    const data = {
        ticketId: ticketId,
        collectorId: collectorId,
        dueDate: dueDate
    };

    try {
        const response = await fetch('php/sub_module_2__Account_Receivable_backend.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(errorText || "Server responded with an error.");
        }

        const result = await response.json();

        if (result.success) {
            showSuccessModal("Assignment saved successfully!");
            fetchDataAndRender();
            closeModal();
        } else {
            console.error(result.message);
            showErrorModal("Error: " + result.message);
        }
    } catch (error) {
        console.error('Error assigning collector:', error);
        showErrorModal("An error occurred. Please try again.");
    }
}

// Function to show the modal and populate the collector dropdown
async function openAssignCollectorModal(ticketId) {
    const modal = document.getElementById('sendReminderModal');
    const ticketIdInput = document.getElementById('ticket-id');
    const collectorSelect = document.getElementById('collector-select');
    const dueDateInput = document.getElementById('due-date');

    ticketIdInput.value = ticketId;
    collectorSelect.value = '';
    dueDateInput.value = '';
    
    try {
        const response = await fetch('php/sub_module_2__Account_Receivable_backend.php?fetch_collectors=true');
        const collectors = await response.json();
        
        collectorSelect.innerHTML = '<option value="">Select a Collector</option>';
        collectors.forEach(collector => {
            const option = document.createElement('option');
            option.value = collector.collector_id;
            option.textContent = collector.collector_name;
            collectorSelect.appendChild(option);
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        toggleSaveButton();
        
    } catch (error) {
        console.error('Error fetching collectors:', error);
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('sendReminderModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function showSuccessModal(message) {
    const modal = document.createElement('div');
    modal.className = "fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900/50 backdrop-blur-sm";
    
    modal.innerHTML = `
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Success!</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">${message}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm" onclick="this.closest('.fixed').remove()">Close</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}
// Function to show a simple error message modal
function showErrorModal(message) {
    const modal = document.createElement('div');
    modal.className = "fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-red-900 bg-opacity-50";
    modal.innerHTML = `
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Error</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">${message}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm" onclick="this.closest('.fixed').remove()">Close</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Function to check if required fields are filled and enable/disable the save button
function toggleSaveButton() {
    const collectorId = document.getElementById('collector-select').value;
    const dueDate = document.getElementById('due-date').value;
    const saveButton = document.getElementById('save-assignment-btn');
    
    if (collectorId && dueDate) {
        saveButton.disabled = false;
    } else {
        saveButton.disabled = true;
    }
}

// --- On-page-load event listeners and initial data fetch ---
$(document).ready(function() {
    // Tab functionality
    $('#analysisTab a').on('click', function (e) {
        e.preventDefault();
        const target = $(this).attr('href');

        $('#analysisTab a').removeClass('text-indigo-600 border-indigo-500').addClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');
        $(this).removeClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300').addClass('text-indigo-600 border-indigo-500');

        $('.tab-pane').removeClass('show active').addClass('hidden');
        $(target).removeClass('hidden').addClass('show active');
    });

    // Modal functionality
    $(document).on('click', '[data-modal-target]', function() {
        const target = $(this).attr('data-modal-target');
        $('#' + target).removeClass('hidden').addClass('flex');
    });

    $(document).on('click', '[data-modal-hide]', function() {
        const target = $(this).closest('.fixed').attr('id');
        $('#' + target).removeClass('flex').addClass('hidden');
    });

    $(document).on('click', '[data-modal-target="viewStatementModal"]', function() {
        const pdfUrl = $(this).data('pdf-url');
        const modal = $('#viewStatementModal');
        const iframe = modal.find('#pdfViewer');
        if (pdfUrl) {
            iframe.attr('src', pdfUrl);
        } else {
            console.error('No PDF URL found for this statement.');
            iframe.attr('src', '');
        }
        modal.removeClass('hidden').addClass('flex');
    });
    
    // Event delegation for the save button click
    $(document).on('click', '#save-assignment-btn:not([disabled])', function() {
        assignCollectorAndTicket();
    });

    // Event listeners for the fields to toggle the save button
    document.getElementById('collector-select').addEventListener('change', toggleSaveButton);
    document.getElementById('due-date').addEventListener('change', toggleSaveButton);
    
    // Initial data fetch on page load
    fetchDataAndRender();
});

// This function populates and opens the view modal
function openViewModal(ticketId) {
    fetch(`php/sub_module_2__Account_Receivable_backend.php?fetch_debtor_details=true&ticket_id=${ticketId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                $('#ticket-modal-title').text(`ticket entry::${data.Ticket_ID}`);
                $('#view-name').text(data.debtor_name || 'N/A');
                $('#view-paid-amount').text(`$${(data.total_paid_amount || 0).toLocaleString()}`);
                $('#view-balance').text(`$${(data.balance || 0).toLocaleString()}`);
                $('#view-last-paid-date').text(data.date_time || 'N/A');
                $('#view-collector').text(data.collector_name || 'N/A');
                $('#view-notification-status').text(data.notification_status || 'N/A');
                $('#view-claim-status').text(data.claim_status || 'N/A');
                
                // Construct and display aging bucket
                let agingBucket = '';
                if (data['30_Days'] > 0) agingBucket = '0-30 days';
                else if (data['60_Days'] > 0) agingBucket = '31-60 days';
                else if (data['90_Days'] > 0) agingBucket = '61-90 days';
                else if (data['down_payment'] > 0) agingBucket = 'Current';
                else agingBucket = 'N/A';
                
                $('#view-aging-bucket').text(agingBucket);
                
                // Add the event listener for the 'View Statement' button inside the modal
                $('#view-statement-btn').attr('data-pdf-url', `php/sub_module_2__Account_Receivable_backend.php?fetch_statement=true&ticket_id=${data.Ticket_ID}`);
                
                // Show the modal
                $('#ticket-modal').removeClass('hidden').addClass('flex');
            } else {
                showErrorModal(result.message);
            }
        })
        .catch(error => {
            console.error('Error fetching debtor details:', error);
            showErrorModal("An error occurred. Please try again.");
        });
}

// Function to handle opening the View Statement modal from the new view modal
$(document).on('click', '#view-statement-btn', function() {
    const pdfUrl = $(this).attr('data-pdf-url');
    if (pdfUrl) {
        const modal = $('#viewStatementModal');
        const iframe = modal.find('#pdfViewer');
        iframe.attr('src', pdfUrl);
        modal.removeClass('hidden').addClass('flex');
    } else {
        console.error('No PDF URL found for this statement.');
    }
});