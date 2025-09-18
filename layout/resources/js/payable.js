// Function to fetch and display data from the backend
function fetchData() {
    // The path is corrected to navigate from the current directory (js)
    // up two levels to the root and then into the 'php' folder.
    fetch('/financial/php/sub_module_2_payable_backend.php')
        .then(response => {
            if (!response.ok) {
                // Throws an error if the network response is not OK (e.g., 404 or 500)
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                const payables = data.data;

                // Update summary dashboard cards
                if (payables.summary) {
                    // Update employee-specific summary cards
                    const employeeTotalPayableSpan = document.getElementById('total-payable').querySelector('span');
                    if (employeeTotalPayableSpan) {
                        employeeTotalPayableSpan.innerHTML = `<i class='bx bx-coin text-3xl text-yellow-900'></i>₱${parseFloat(payables.summary.total_payable).toFixed(2)}`;
                    }
    
                    const employeeCostSpan = document.getElementById('employee-cost');
                    if(employeeCostSpan) {
                        employeeCostSpan.textContent = `₱${parseFloat(payables.summary.employee_cost).toFixed(2)}`;
                    }
    
                    const employeeRequestsSpan = document.getElementById('employee-requests');
                    if (employeeRequestsSpan) {
                        employeeRequestsSpan.textContent = `${payables.summary.total_employee_requests}`;
                    }
    
                    // Update vendor-specific summary cards
                    const vendorTotalPayableSpan = document.getElementById('vendor-total-payable').querySelector('span');
                    if (vendorTotalPayableSpan) {
                        vendorTotalPayableSpan.innerHTML = `<i class='bx bx-coin text-3xl text-yellow-900'></i>₱${parseFloat(payables.summary.total_payable).toFixed(2)}`;
                    }
    
                    const vendorCostSpan = document.getElementById('vendor-cost').querySelector('span');
                    if (vendorCostSpan) {
                        vendorCostSpan.innerHTML = `<i class='bx bx-money text-3xl text-orange-900'></i>₱${parseFloat(payables.summary.vendor_cost).toFixed(2)}`;
                    }
                    
                    const vendorRequestsSpan = document.getElementById('vendor-requests').querySelector('span');
                    if (vendorRequestsSpan) {
                        vendorRequestsSpan.innerHTML = `<i class='bx bx-file-blank text-3xl text-amber-900'></i>${payables.summary.total_vendor_requests}`;
                    }
                }
                // Populate employee table
                const employeeTableBody = document.getElementById('records-table-body-employee');
                employeeTableBody.innerHTML = '';
                if (payables.employee_payables.length > 0) {
                    payables.employee_payables.forEach(row => {
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-employee', JSON.stringify(row));
                        newRow.innerHTML = `
                            <td>${row.Employee_ID}</td>
                            <td>${row.First_Name || ''} ${row.Last_Name || 'N/A'}</td>
                            <td>₱${parseFloat(row.Requested_Amount).toFixed(2)}</td>
                            <td>${row.Due_Date || 'N/A'}</td>
                            <td>${row.priority || 'N/A'}</td>
                            <td class="actions-cell">
                                <button onclick="showModal('employee', this)">Review</button>
                            </td>
                        `;
                        employeeTableBody.appendChild(newRow);
                    });
                } else {
                    const noRecordsRow = document.createElement('tr');
                    noRecordsRow.innerHTML = `<td colspan="6" class="text-center">No employee records found.</td>`;
                    employeeTableBody.appendChild(noRecordsRow);
                }

                // Populate vendor table
                const vendorTableBody = document.getElementById('records-table-body-vendor');
                vendorTableBody.innerHTML = '';
                if (payables.vendor_payables.length > 0) {
                    payables.vendor_payables.forEach(row => {
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-vendor', JSON.stringify(row));
                        newRow.innerHTML = `
                            <td>${row.Vendor_ID}</td>
                            <td>${row.Company_Name || 'N/A'}</td>
                            <td>₱${parseFloat(row.Request_Amount).toFixed(2)}</td>
                            <td>${row.Due_Date || 'N/A'}</td>
                            <td>${row.priority || 'N/A'}</td>
                            <td class="actions-cell">
                                <button onclick="showModal('vendor', this)">Review</button>
                            </td>
                        `;
                        vendorTableBody.appendChild(newRow);
                    });
                } else {
                    const noRecordsRow = document.createElement('tr');
                    noRecordsRow.innerHTML = `<td colspan="6" class="text-center">No vendor records found.</td>`;
                    vendorTableBody.appendChild(noRecordsRow);
                }
                
            } else {
                console.error('Backend Error:', data.message);
                const tableBodies = ['records-table-body-employee', 'records-table-body-vendor'];
                tableBodies.forEach(id => {
                    const tableBody = document.getElementById(id);
                    if (tableBody) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-red-500">Error loading data.</td></tr>`;
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            const tableBodies = ['records-table-body-employee', 'records-table-body-vendor'];
            tableBodies.forEach(id => {
                const tableBody = document.getElementById(id);
                if (tableBody) {
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-red-500">Error loading data.</td></tr>`;
                }
            });
        });
}

// Call the function to load data when the page loads and attach the event listener for the reject button
window.onload = function() {
    fetchData();
    const confirmRejectBtn = document.getElementById('confirmRejectBtn');
    if (confirmRejectBtn) {
        confirmRejectBtn.addEventListener('click', handleReject);
    }
};

function showTab(tabName, element) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show the selected tab content and add active class to the clicked button
    document.getElementById(tabName + '-tab').classList.add('active');
    element.classList.add('active');

    // Update the dashboard title based on the selected tab
    const dashboardTitle = document.getElementById('dashboard-title');
    const companyFilterContainer = document.getElementById('company-filter-container');


    // Title ng Dashboard
    if (tabName === 'employee') {
        dashboardTitle.textContent = "Payable Dashboard: Employee Only";
    } else if (tabName === 'vendor') {
        dashboardTitle.textContent = "Payable Dashboard: Supplier Only";
    }
}
function showModal(type, button) {
    const row = button.closest('tr');
    
    // Determine which modal to show based on type
    if (type === 'vendor') {
        const vendorData = JSON.parse(row.getAttribute('data-vendor'));
        const modal = document.getElementById('vendor-modal');
        const pdfButton = modal.querySelector('.pdf-uploaded-btn');
        const imageElement = modal.querySelector('.modal-image');
        
        // Populate the modal with the vendor's information
        modal.querySelector('p:nth-child(1) strong').textContent = `Vendor Name: ${vendorData.First_Name || ''} ${vendorData.Last_Name || ''}`;
        modal.querySelector('p:nth-child(2) strong').textContent = `Company Name: ${vendorData.Company_Name || 'N/A'}`;
        modal.querySelector('p:nth-child(3) strong').textContent = `Contact number: ${vendorData.contact_number || 'N/A'}`;
        modal.querySelector('p:nth-child(4) strong').textContent = `Email: ${vendorData.Email || 'N/A'}`;
        modal.querySelector('p:nth-child(5) strong').textContent = `Company Address: ${vendorData.Address || 'N/A'}`;
        modal.querySelector('p:nth-child(6) strong').textContent = `Amount Requested: ₱${parseFloat(vendorData.Request_Amount).toFixed(2)}`;
        modal.querySelector('p:nth-child(7) strong').textContent = `Due date: ${vendorData.Due_Date || 'N/A'}`;
        modal.querySelector('p:nth-child(8) strong').textContent = `Payment Method: ${vendorData.payment_method || 'N/A'}`;
        modal.querySelector('p:nth-child(9) strong').textContent = `Purpose: ${vendorData.purpose || 'N/A'}`;
        modal.querySelector('#vendor-priority-badge').textContent = `Priority ${vendorData.priority || 'N/A'}`;

        // Set the status badge text and color
        const vendorStatusBadge = modal.querySelector('#vendor-status-badge');
        vendorStatusBadge.textContent = `Allocated Status: ${vendorData.allocation_status.status}`;
        vendorStatusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white ${vendorData.allocation_status.color}`;

        // Update the PDF button click handler to include the document data
        if (vendorData.Document) {
            pdfButton.onclick = () => showPdfModal(vendorData.Document);
            pdfButton.style.display = 'block';
        } else {
            pdfButton.style.display = 'none';
        }

        // Set the image source if the picture data exists
        if (vendorData.profile_picture) {
            imageElement.src = 'data:image/jpeg;base64,' + vendorData.profile_picture;
            imageElement.style.display = 'block';
        } else {
            imageElement.style.display = 'none';
        }

        const actionButtonsContainer = modal.querySelector('.modal-actions');
        if (actionButtonsContainer) {
            const newButtonsContainer = actionButtonsContainer.cloneNode(true);
            actionButtonsContainer.parentNode.replaceChild(newButtonsContainer, actionButtonsContainer);

            newButtonsContainer.addEventListener('click', (event) => {
                const button = event.target.closest('button');
                if (button) {
                    const action = button.dataset.action;
                    const payableId = vendorData.Vendor_ID;
                    const type = 'vendor';
                    if (action === 'reject') {
                        openRejectModal(payableId, type);
                    } else {
                        handleApproveReject(payableId, type, action);
                    }
                }
            });
        }
        
        // Show the modal
        modal.classList.add('is-visible');

    } else if (type === 'employee') {
        const employeeData = JSON.parse(row.getAttribute('data-employee'));
        const modal = document.getElementById('employee-modal');
        const pdfButton = modal.querySelector('.pdf-uploaded-btn');
        const imageElement = modal.querySelector('.modal-image');
        
        // Populate the modal with the employee's information
        modal.querySelector('p:nth-child(1) strong').textContent = `Employee Name: ${employeeData.First_Name || ''} ${employeeData.Last_Name || ''}`;
        modal.querySelector('p:nth-child(2) strong').textContent = `Position: ${employeeData.Job_Tittle || 'N/A'}`;
        modal.querySelector('p:nth-child(3) strong').textContent = `Department: ${employeeData.department || 'N/A'}`;
        modal.querySelector('p:nth-child(4) strong').textContent = `Age: ${employeeData.Age || 'N/A'}`;
        modal.querySelector('p:nth-child(5) strong').textContent = `Gender: ${employeeData.Gender || 'N/A'}`;
        modal.querySelector('p:nth-child(6) strong').textContent = `Email: ${employeeData.Email || 'N/A'}`;
        modal.querySelector('p:nth-child(7) strong').textContent = `Address: ${employeeData.Address || 'N/A'}`;
        modal.querySelector('p:nth-child(8) strong').textContent = `Amount Requested: ₱${parseFloat(employeeData.Requested_Amount).toFixed(2)}`;
        modal.querySelector('p:nth-child(9) strong').textContent = `Due date: ${employeeData.Due_Date || 'N/A'}`;
        modal.querySelector('p:nth-child(10) strong').textContent = `Payment Method: ${employeeData.payment_method || 'N/A'}`;
        modal.querySelector('p:nth-child(11) strong').textContent = `Justification: ${employeeData.justification || 'N/A'}`;
        modal.querySelector('#employee-priority-badge').textContent = `Priority ${employeeData.priority || 'N/A'}`;
        
        // Attach event listeners to the action buttons
        const actionButtonsContainer = document.getElementById('employee-modal').querySelector('.modal-actions');
            if (actionButtonsContainer) {
                // Remove any existing listeners to prevent duplicates
                const newButtonsContainer = actionButtonsContainer.cloneNode(true);
                actionButtonsContainer.parentNode.replaceChild(newButtonsContainer, actionButtonsContainer);

                newButtonsContainer.addEventListener('click', (event) => {
                    const button = event.target.closest('button');
                    if (button) {
                        const action = button.dataset.action;
                        const payableId = employeeData.Employee_ID;
                        const type = 'employee';
                        if (action === 'reject') {
                            openRejectModal(payableId, type);
                        } else {
                            handleApproveReject(payableId, type, action);
                        }
                    }
                });
            }
        // Set the status badge text and color
        const employeeStatusBadge = modal.querySelector('#employee-status-badge');
        employeeStatusBadge.textContent = `Allocated Status: ${employeeData.allocation_status.status}`;
        employeeStatusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white ${employeeData.allocation_status.color}`;

        // Update the PDF button click handler to include the document data
        if (employeeData.Document) {
            pdfButton.onclick = () => showPdfModal(employeeData.Document);
            pdfButton.style.display = 'block';
        } else {
            pdfButton.style.display = 'none';
        }

        // Set the image source if the picture data exists
        if (employeeData.profile_picture) {
            imageElement.src = 'data:image/jpeg;base64,' + employeeData.profile_picture;
            imageElement.style.display = 'block';
        } else {
            imageElement.style.display = 'none';
        }

        // Show the modal
        modal.classList.add('is-visible');
    }
}
function hideModal(type) {
    document.getElementById(type + '-modal').classList.remove('is-visible');
}

function changeText(element, text) {
    element.querySelector('span').textContent = text;
}

function showFilterModal(tabType) {
    const companyFilterContainer = document.getElementById('company-filter-container');
    if (tabType === 'employee') {
        companyFilterContainer.classList.add('hidden');
    } else {
        companyFilterContainer.classList.remove('hidden');
    }
    document.getElementById('filter-modal').classList.remove('hidden');
}

function hideFilterModal() {
    document.getElementById('filter-modal').classList.add('hidden');
}

function applyFilter() {
    // Implement your filtering logic here.
    // Get the selected values from the dropdowns:
    const dateOrder = document.getElementById('date-filter').value;
    const timeOrder = document.getElementById('time-filter').value;
    const companyName = document.getElementById('company-filter').value;
    
    console.log('Applying filters:', { dateOrder, timeOrder, companyName });
    
    // After applying the filters, you would typically refresh the table data.
    // For now, we will just hide the modal.
    hideFilterModal();
}

function filterTable(tabName) {
    const searchInput = document.getElementById(tabName + '-search');
    const filter = searchInput.value.toLowerCase();
    const tableBody = document.getElementById('records-table-body-' + tabName);
    const rows = tableBody.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(filter)) {
                found = true;
                break;
            }
        }
        if (found) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Function to show the PDF modal
function showPdfModal(documentBlob) {
    const modal = document.getElementById('viewStatementModal');
    const pdfViewer = document.getElementById('pdfViewer');
    
    // Check if a document blob was passed and if it's not null
    if (documentBlob) {
        // Decode the Base64 string to a binary string
        const binaryString = atob(documentBlob);
        const len = binaryString.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }

        // Create a blob from the document data
        const blob = new Blob([bytes.buffer], { type: 'application/pdf' });
        // Create a URL for the blob
        const url = URL.createObjectURL(blob);
        // Set the iframe's source to the blob URL
        pdfViewer.src = url;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } else {
        // Hide the PDF modal if no document is available
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        alert('No document available for this record.');
    }
}

// Function to hide the PDF modal
function hidePdfModal() {
    const modal = document.getElementById('viewStatementModal');
    const pdfViewer = document.getElementById('pdfViewer');
    // Revoke the blob URL to free up memory
    if (pdfViewer.src) {
        URL.revokeObjectURL(pdfViewer.src);
    }
    pdfViewer.src = '';
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
// Function to open the reject modal
function openRejectModal(id, type) {
    const rejectModal = document.getElementById('rejectModal');
    const confirmRejectBtn = document.getElementById('confirmRejectBtn');
    
    // Set the data attributes on the confirm button
    confirmRejectBtn.setAttribute('data-payable-id', id);
    confirmRejectBtn.setAttribute('data-type', type);

    // Show the reject modal
    rejectModal.classList.remove('hidden');
    rejectModal.classList.add('flex');
}

// Function to close the reject modal
function closeRejectModal() {
    const rejectModal = document.getElementById('rejectModal');
    rejectModal.classList.add('hidden');
    rejectModal.classList.remove('flex');
    document.getElementById('rejectReasonInput').value = ''; // Clear the input
}

// Function to handle the rejection
function handleReject() {
    const confirmRejectBtn = document.getElementById('confirmRejectBtn');
    const payableId = confirmRejectBtn.getAttribute('data-payable-id');
    const type = confirmRejectBtn.getAttribute('data-type');
    const reason = document.getElementById('rejectReasonInput').value;

    if (!reason.trim()) {
        alert('Please provide a reason for rejection.');
        return;
    }

    // Call the approve/reject function with the reject action and reason
    handleApproveReject(payableId, type, 'reject', reason);

    // Close the modals
    closeRejectModal();
    hideModal(type);
}

// Function to handle approve/reject actions
function handleApproveReject(id, type, action, reason = null) {
    // Add a confirmation for the approve action
    if (action === 'approve') {
        if (!confirm('Are you sure you want to approve this request?')) {
            return; // Exit the function if the user cancels
        }
    }

    const formData = new FormData();
    formData.append('payable_id', id);
    formData.append('type', type);
    formData.append('action', action);
    if (reason) {
        formData.append('reason', reason);
    }
    
    fetch('/financial/php/sub_module_2_payable_backend.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const modalId = type === 'employee' ? 'employee-modal' : 'vendor-modal';
        const mainModal = document.getElementById(modalId);
        
        // Hide the current modal
        if (mainModal) {
            mainModal.classList.remove('is-visible');
        }

        const ticketModal = document.getElementById('ticketModal');
        const ticketModalTitle = document.getElementById('ticketModalTitle');
        const ticketEntry = document.getElementById('ticketEntry');
        const ticketMessage = document.getElementById('ticketMessage');

        if (data.status === 'success') {
            const message = action === 'approve' ? '✅ Approved successfully!' : '❌ Rejected successfully';
            ticketModalTitle.textContent = 'Ticket Generated';
            ticketEntry.textContent = data.ticket_entry;
            ticketMessage.textContent = message;
            ticketMessage.classList.add('text-green-600');
            ticketMessage.classList.remove('text-red-600');
            fetchData(); // Refresh the table data
        } else {
            ticketModalTitle.textContent = 'Error';
            ticketEntry.textContent = 'N/A';
            ticketMessage.textContent = data.message;
            ticketMessage.classList.add('text-red-600');
            ticketMessage.classList.remove('text-green-600');
        }

        ticketModal.classList.remove('hidden');
        ticketModal.classList.add('flex');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
// Function to close the main ticket modal
function closeTicketModal() {
    const ticketModal = document.getElementById('ticketModal');
    ticketModal.classList.add('hidden');
    ticketModal.classList.remove('flex');
}