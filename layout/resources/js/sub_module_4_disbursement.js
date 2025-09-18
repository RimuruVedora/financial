document.addEventListener('DOMContentLoaded', () => {
    // Modal Elements
    const confirmModal = document.getElementById('confirmModal');
    const finalModal = document.getElementById('finalModal');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelConfirmBtn = document.getElementById('cancelConfirmBtn');

    // ✅ Open Disbursement Modal with data
    function openDisbursementModal(item, type) {
        if (!confirmModal) return;

        // Name
        document.getElementById('name').value =
            type === 'employee' ? item.full_name : item.name;

        // Requested Amount & Paid Amount
        document.getElementById('requested-amount').value = item.Requested_Amount || 0;
        document.getElementById('amount').value = item.Requested_Amount || 0;

        // Hidden Ticket Entry
        if (document.getElementById('modalTicketEntry')) {
            document.getElementById('modalTicketEntry').value = item.ticket_Entry;
        }

        // Payable Type
        const payableType = document.getElementById('payable-type');
        if (type === 'employee') {
            payableType.innerHTML = `
                <option ${item.payable_type === 'Salary' ? 'selected' : ''}>Salary</option>
                <option ${item.payable_type === 'Reimbursement' ? 'selected' : ''}>Reimbursement</option>
                <option ${item.payable_type === 'Payroll' ? 'selected' : ''}>Payroll</option>
            `;
        } else {
            payableType.innerHTML = `<option selected>Purchase Order</option>`;
        }

        // Payment Method
        document.getElementById('payment-method').value =
            item.payment_method || 'Cash';

        // Show modal
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
    }

    // ✅ Handle View button clicks (delegated listener)
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.view-btn');
        if (button) {
            const item = JSON.parse(button.getAttribute('data-item')); // full object
            const type = button.getAttribute('data-type'); // employee or vendor
            openDisbursementModal(item, type);
        }
    });

    // Event listener for the modal's confirm button
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            confirmModal.classList.add('hidden');
            finalModal.classList.remove('hidden');
            finalModal.classList.add('flex');
        });
    }

    // Event listener for the modal's cancel button
    if (cancelConfirmBtn) {
        cancelConfirmBtn.addEventListener('click', () => {
            confirmModal.classList.add('hidden');
        });
    }

    // ✅ Final confirmation - submit disbursement
    async function submitDisbursement() {
        const ticketEntry = document.getElementById('modalTicketEntry').value;
        const requestedAmount = parseFloat(document.getElementById('requested-amount').value) || 0;
        const paidAmount = parseFloat(document.getElementById('amount').value) || 0;

        try {
            const response = await fetch('php/sub_module_4_disbursment_backend.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ticket_Entry: ticketEntry,
                    Requested_Amount: requestedAmount,
                    paid_amount: paidAmount
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('Transaction successfully approved!');
                fetchDataAndPopulateTables(); // 🔄 Refresh dashboard numbers + tables
            } else {
                alert('Error: ' + result.message);
            }
        } catch (err) {
            console.error('Error submitting disbursement:', err);
            alert('Something went wrong.');
        }

        finalModal.classList.add('hidden');
    }

    // Expose globally for inline onclick
    window.submitDisbursement = submitDisbursement;

    // ✅ Fetch and populate tables + cards
    async function fetchDataAndPopulateTables() {
        try {
            const response = await fetch('php/sub_module_4_disbursment_backend.php');
            const data = await response.json();

            // Populate table contents
            populateTable('employees-table-body', data.employees, 'employee');
            populateTable('vendors-table-body', data.vendors, 'vendor');

            // Update Pending card
            const pendingCard = document.getElementById('pending-count');
            if (pendingCard) {
                pendingCard.textContent = data.pending_count || 0;
            }

            // Update Completed card
            const completedCard = document.getElementById('completed-count');
            if (completedCard) {
                completedCard.textContent = data.completed_count || 0;
            }

            // Update Total Disbursement card
            const totalDisbursementCard = document.getElementById('total-disbursement');
            if (totalDisbursementCard) {
                totalDisbursementCard.textContent =
                    `₱${(data.total_disbursement || 0).toLocaleString()}`;
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            const employeesBody = document.getElementById('employees-table-body');
            const vendorsBody = document.getElementById('vendors-table-body');
            if (employeesBody) employeesBody.innerHTML = `<tr><td colspan="7" class="text-center">Error loading employee data.</td></tr>`;
            if (vendorsBody) vendorsBody.innerHTML = `<tr><td colspan="6" class="text-center">Error loading vendor data.</td></tr>`;
        }
    }

    // ✅ Populate table rows
    function populateTable(tableId, data, type) {
        const tableBody = document.getElementById(tableId);
        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (!data || data.length === 0) {
            const row = tableBody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = (type === 'employee') ? 7 : 6;
            cell.className = 'text-center py-4';
            cell.textContent = `No ${type} data with 'onhand' status.`;
            return;
        }

        data.forEach(item => {
            const row = tableBody.insertRow();
            row.innerHTML = `
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">${item.full_name || item.name}</td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">${item.ticket_Entry || 'N/A'}</td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">${item.contact_number}</td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">${item.payment_method}</td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">${item.due_date}</td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">onhand</td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                    <button class="btn btn-sm btn-success view-btn"
                        data-type="${type}"
                        data-item='${JSON.stringify(item)}'>View</button>
                </td>
            `;
        });
    }

    // ✅ Load data on page load
    fetchDataAndPopulateTables();
});
