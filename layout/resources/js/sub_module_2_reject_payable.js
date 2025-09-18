document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const vendorTableBody = document.getElementById('vendor-tableBody');
    const vendorSearchInput = document.getElementById('vendor-searchInput');
    const vendorTotalRejectedEl = document.getElementById('total-rejected');
    const vendorPayableRejectedEl = document.getElementById('payable-rejected');
    const employeeTableBody = document.getElementById('employee-tableBody');
    const employeeSearchInput = document.getElementById('employee-searchInput');
    const employeeTotalRejectedEl = document.getElementById('employee-total-rejected');
    const employeePayableRejectedEl = document.getElementById('employee-payable-rejected');

    // Tab Elements
    const vendorTab = document.getElementById('vendor-tab');
    const employeeTab = document.getElementById('employee-tab');
    const vendorContent = document.getElementById('vendor-content');
    const employeeContent = document.getElementById('employee-content');

    // Modal Elements
    const viewModal = document.getElementById('viewModal');
    const closeModalBtn = document.getElementById('closeModal');
    const modalTitle = document.getElementById('modal-title');
    const vendorDetails = document.getElementById('vendor-details');
    const employeeDetails = document.getElementById('employee-details');
    const rejectReasonEl = document.getElementById('modal-reason-to-reject');

    // Store fetched data
    let allData = {
        vendor: { tickets: [] },
        employee: { tickets: [] }
    };

    // --- Data Fetching ---
    async function fetchData() {
        try {
            const response = await fetch('php/sub_module_2_payable_reject_backend.php');
            const data = await response.json();
            if (data.success) {
                allData = data;
                // Render the initial active tab (Vendor)
                renderTable(allData.vendor.tickets, vendorTableBody);
                updateDashboard(allData.vendor.totalRejected, allData.vendor.totalPayableRejected, vendorTotalRejectedEl, vendorPayableRejectedEl);
                console.log("Data fetched successfully:", allData);
            } else {
                console.error("Failed to fetch data:", data.message);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    }

    // --- Core Functions ---
    function renderTable(tickets, tableBody) {
        tableBody.innerHTML = '';
        if (tickets.length > 0) {
            tickets.forEach(ticket => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition-colors duration-200';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${ticket.ticket_Entry}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center">
                            <i class='bx bxs-x-circle text-lg mr-2 text-red-500'></i>
                            <span class="text-red-500">Rejected</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${ticket.REASON || 'No reason provided'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full view-button flex items-center justify-center" data-ticket-id="${ticket.ticket_Entry}">
                            <i class='bx bx-show-alt text-lg mr-2'></i> View
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No rejected tickets found.</td></tr>';
        }
    }

    function updateDashboard(totalRejected, payableRejected, totalRejectedEl, payableRejectedEl) {
        if (totalRejectedEl) totalRejectedEl.textContent = totalRejected;
        if (payableRejectedEl) payableRejectedEl.textContent = `₱${parseFloat(payableRejected).toFixed(2)}`;
    }

    // --- Modal Functionality ---
    function showModal(type, data) {
        if (vendorDetails) vendorDetails.classList.add('hidden');
        if (employeeDetails) employeeDetails.classList.add('hidden');

        if (type === 'vendor') {
            if (modalTitle) modalTitle.textContent = 'Vendor Details';
            if (vendorDetails) vendorDetails.classList.remove('hidden');

            const modalVendorName = document.getElementById('modal-vendor-name');
            if (modalVendorName) modalVendorName.textContent = `${data.First_Name || ''} ${data.Middle_Name || ''} ${data.Last_Name || ''}`.trim();
            const modalCompanyName = document.getElementById('modal-company-name');
            if (modalCompanyName) modalCompanyName.textContent = data.Company_Name || 'N/A';
            const modalContactNumber = document.getElementById('modal-contact-number');
            if (modalContactNumber) modalContactNumber.textContent = data.contact_number || 'N/A';
            const modalVendorEmail = document.getElementById('modal-vendor-email');
            if (modalVendorEmail) modalVendorEmail.textContent = data.Email || 'N/A';
            const modalCompanyAddress = document.getElementById('modal-company-address');
            if (modalCompanyAddress) modalCompanyAddress.textContent = data.Address || 'N/A';
            const modalVendorAmount = document.getElementById('modal-vendor-amount-requested');
            if (modalVendorAmount) modalVendorAmount.textContent = `₱${parseFloat(data.Request_Amount || 0).toFixed(2)}`;
            const modalVendorDueDate = document.getElementById('modal-vendor-due-date');
            if (modalVendorDueDate) modalVendorDueDate.textContent = data.due_date || 'N/A';
            const modalVendorPaymentMethod = document.getElementById('modal-vendor-payment-method');
            if (modalVendorPaymentMethod) modalVendorPaymentMethod.textContent = data.payment_method || 'N/A';
            const modalPurpose = document.getElementById('modal-purpose');
            if (modalPurpose) modalPurpose.textContent = data.purpose || 'N/A';
        } else if (type === 'employee') {
            if (modalTitle) modalTitle.textContent = 'Employee Details';
            if (employeeDetails) employeeDetails.classList.remove('hidden');

            const modalEmployeeName = document.getElementById('modal-employee-name');
            if (modalEmployeeName) modalEmployeeName.textContent = `${data.First_Name || ''} ${data.Middle_Name || ''} ${data.Last_Name || ''}`.trim();
            const modalPosition = document.getElementById('modal-position');
            if (modalPosition) modalPosition.textContent = data.position || 'N/A';
            const modalDepartment = document.getElementById('modal-department');
            if (modalDepartment) modalDepartment.textContent = data.department || 'N/A';
            const modalAge = document.getElementById('modal-age');
            if (modalAge) modalAge.textContent = data.Age || 'N/A';
            const modalGender = document.getElementById('modal-gender');
            if (modalGender) modalGender.textContent = data.Gender || 'N/A';
            const modalEmployeeEmail = document.getElementById('modal-employee-email');
            if (modalEmployeeEmail) modalEmployeeEmail.textContent = data.Email || 'N/A';
            const modalAddress = document.getElementById('modal-address');
            if (modalAddress) modalAddress.textContent = data.Address || 'N/A';
            const modalEmployeeAmount = document.getElementById('modal-employee-amount-requested');
            if (modalEmployeeAmount) modalEmployeeAmount.textContent = `₱${parseFloat(data.Requested_Amount || 0).toFixed(2)}`;
            const modalEmployeeDueDate = document.getElementById('modal-employee-due-date');
            if (modalEmployeeDueDate) modalEmployeeDueDate.textContent = data.due_date || 'N/A';
            const modalEmployeePaymentMethod = document.getElementById('modal-employee-payment-method');
            if (modalEmployeePaymentMethod) modalEmployeePaymentMethod.textContent = data.payment_method || 'N/A';
            const modalJustification = document.getElementById('modal-justification');
            if (modalJustification) modalJustification.textContent = data.justification || 'N/A';
        }

        if (rejectReasonEl) rejectReasonEl.textContent = data.REASON || 'N/A';

        // Show the modal by removing the 'hidden' class
        if (viewModal) {
            viewModal.classList.remove('hidden');
        }
    }

    // Function to close the modal
    function closeModal() {
        if (viewModal) {
            viewModal.classList.add('hidden');
        }
    }

    // --- Event Listeners ---
    if (vendorTab && employeeTab && vendorContent && employeeContent) {
        vendorTab.addEventListener('click', (event) => {
            if (!vendorTab.classList.contains('tab-active')) {
                vendorContent.classList.add('active');
                employeeContent.classList.remove('active');
                vendorTab.classList.add('tab-active');
                employeeTab.classList.remove('tab-active');
                if (vendorTableBody) renderTable(allData.vendor.tickets, vendorTableBody);
                updateDashboard(allData.vendor.totalRejected, allData.vendor.totalPayableRejected, vendorTotalRejectedEl, vendorPayableRejectedEl);
            }
        });

        employeeTab.addEventListener('click', (event) => {
            if (!employeeTab.classList.contains('tab-active')) {
                employeeContent.classList.add('active');
                vendorContent.classList.remove('active');
                employeeTab.classList.add('tab-active');
                vendorTab.classList.remove('tab-active');
                if (employeeTableBody) renderTable(allData.employee.tickets, employeeTableBody);
                updateDashboard(allData.employee.totalRejected, allData.employee.totalPayableRejected, employeeTotalRejectedEl, employeePayableRejectedEl);
            }
        });
    } else {
        console.error("Tab or content elements not found. Check your HTML IDs.");
    }

    if (vendorSearchInput) {
        vendorSearchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredTickets = allData.vendor.tickets.filter(ticket =>
                (ticket.ticket_Entry && ticket.ticket_Entry.toLowerCase().includes(searchTerm)) ||
                (ticket.REASON && ticket.REASON.toLowerCase().includes(searchTerm))
            );
            renderTable(filteredTickets, vendorTableBody);
        });
    }

    if (employeeSearchInput) {
        employeeSearchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredTickets = allData.employee.tickets.filter(ticket =>
                (ticket.ticket_Entry && ticket.ticket_Entry.toLowerCase().includes(searchTerm)) ||
                (ticket.REASON && ticket.REASON.toLowerCase().includes(searchTerm))
            );
            renderTable(filteredTickets, employeeTableBody);
        });
    }

    if (vendorTableBody) {
        vendorTableBody.addEventListener('click', function(event) {
            const viewBtn = event.target.closest('.view-button');
            if (viewBtn) {
                const ticketId = viewBtn.dataset.ticketId;
                const vendorTicket = allData.vendor.tickets.find(t => t.ticket_Entry === ticketId);
                if (vendorTicket) {
                    showModal('vendor', vendorTicket);
                }
            }
        });
    }
    if (employeeTableBody) {
        employeeTableBody.addEventListener('click', function(event) {
            const viewBtn = event.target.closest('.view-button');
            if (viewBtn) {
                const ticketId = viewBtn.dataset.ticketId;
                const employeeTicket = allData.employee.tickets.find(t => t.ticket_Entry === ticketId);
                if (employeeTicket) {
                    showModal('employee', employeeTicket);
                }
            }
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    if (viewModal) {
        viewModal.addEventListener('click', function(event) {
            if (event.target === viewModal) {
                closeModal();
            }
        });
    }

    // Initial load
    fetchData();
});