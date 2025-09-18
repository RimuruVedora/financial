const dataList = document.getElementById('dataList');
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearch');
const modal = document.getElementById('modal');
const closeModalBtn = document.getElementById('closeModalBtn');
const approveBtn = document.getElementById('approveBtn');
const rejectBtn = document.getElementById('rejectBtn');
const loadingIndicator = document.getElementById('loadingIndicator');
const approveModal = document.getElementById('approveModal');
const confirmApproveBtn = document.getElementById('confirmApproveBtn');
const cancelApproveBtn = document.getElementById('cancelApproveBtn');
const rejectModal = document.getElementById('rejectModal');
const submitRejectBtn = document.getElementById('submitRejectBtn');
const cancelRejectBtn = document.getElementById('cancelRejectBtn');
const rejectReasonInput = document.getElementById('rejectReasonInput');
const rejectionReasonContainer = document.getElementById('rejectionReasonContainer');
const actionButtons = document.getElementById('actionButtons');
const noActionMessage = document.getElementById('noActionMessage');

let budgetPlans = [];
let currentItemId = null;

async function fetchData() {
    loadingIndicator.classList.remove('hidden');
    try {
        // Fetch only pending plans
        const response = await fetch('php/sub_module3_budget_plan_backend.php?action=get_pending_budget_plans');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        budgetPlans = data;
        renderData(budgetPlans);
        updateMetrics(budgetPlans);
    } catch (error) {
        console.error('Error fetching data:', error);
        dataList.innerHTML = '<div class="text-center text-red-500">Failed to load data. Please try again.</div>';
    } finally {
        loadingIndicator.classList.add('hidden');
    }
}

async function updateStatus(id, status, reason = null) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);
        if (reason) {
            formData.append('rejection_reason', reason);
        }

        const response = await fetch('php/sub_module3_budget_plan_backend.php?action=update_status', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        if (result.success) {
            closeModal();
            closeApproveModal();
            closeRejectModal();
            fetchData(); // Refresh the data
        } else {
            console.error('Failed to update status:', result.error);
        }
    } catch (error) {
        console.error('Error updating status:', error);
    }
}

function updateMetrics(data) {
    // This function now uses the entire dataset to calculate metrics, not just the filtered one.
    const totalProposedBudget = data.reduce((sum, item) => sum + parseFloat(item.proposed_amount), 0);
    const departmentsCount = new Set(data.map(item => item.department)).size;
    const pendingPlansCount = data.filter(item => item.status === 'pending').length;

    document.getElementById('totalProposedBudget').textContent = '₱ ' + totalProposedBudget.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('departmentsCount').textContent = departmentsCount + ' departments';
    document.getElementById('pendingPlansCount').textContent = pendingPlansCount + ' plans';
}

function openModal(item) {
    currentItemId = item.budget_plan_id;
    document.getElementById('modalDepartment').value = item.department;
    document.getElementById('modalSubmittedBy').value = item.submitted_by;
    document.getElementById('modalProposedAmount').value = '₱ ' + parseFloat(item.proposed_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('modalFiscalYear').value = item.fiscal_year;
    document.getElementById('modalJustification').value = item.justification;
    document.getElementById('modalAttachedFile').value = item.attached_file;

    if (item.status === 'rejected') {
        rejectionReasonContainer.classList.remove('hidden');
        document.getElementById('modalRejectionReason').value = item.rejection_reason || 'N/A';
    } else {
        rejectionReasonContainer.classList.add('hidden');
    }

    if (item.status === 'pending') {
        actionButtons.classList.remove('hidden');
        noActionMessage.classList.add('hidden');
    } else {
        actionButtons.classList.add('hidden');
        noActionMessage.classList.remove('hidden');
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    modal.classList.add('hidden');
}

function openApproveModal() {
    approveModal.classList.remove('hidden');
}

function closeApproveModal() {
    approveModal.classList.add('hidden');
}

function openRejectModal() {
    rejectModal.classList.remove('hidden');
}

function closeRejectModal() {
    rejectModal.classList.add('hidden');
    rejectReasonInput.value = '';
}

approveBtn.addEventListener('click', () => {
    closeModal();
    openApproveModal();
});

rejectBtn.addEventListener('click', () => {
    closeModal();
    openRejectModal();
});

confirmApproveBtn.addEventListener('click', () => {
    if (currentItemId) {
        updateStatus(currentItemId, 'approved');
    }
});

cancelApproveBtn.addEventListener('click', () => {
    closeApproveModal();
    openModal(budgetPlans.find(p => p.budget_plan_id === currentItemId));
});

submitRejectBtn.addEventListener('click', () => {
    const reason = rejectReasonInput.value.trim();
    if (reason && currentItemId) {
        updateStatus(currentItemId, 'rejected', reason);
    } else {
        alert('Please provide a reason for rejection.');
    }
});

cancelRejectBtn.addEventListener('click', () => {
    closeRejectModal();
    openModal(budgetPlans.find(p => p.budget_plan_id === currentItemId));
});

closeModalBtn.addEventListener('click', closeModal);

function renderData(items) {
    dataList.innerHTML = '';
    if (items.length === 0) {
        dataList.innerHTML = '<div class="text-center text-gray-500">No data found.</div>';
        return;
    }
    items.forEach(item => {
        const row = document.createElement('div');
        row.className = 'grid grid-cols-2 md:grid-cols-6 gap-4 items-center py-4 border-b border-gray-100 last:border-b-0';
        
        let statusColor;
        switch (item.status) {
            case 'approved':
                statusColor = 'text-green-600';
                break;
            case 'pending':
                statusColor = 'text-yellow-600';
                break;
            case 'rejected':
                statusColor = 'text-red-600';
                break;
            default:
                statusColor = 'text-gray-600';
        }

        row.innerHTML = `
            <span class="font-bold md:font-normal block md:hidden">Department: </span><span class="md:col-span-1">${item.department}</span>
            <span class="hidden md:block">${item.fiscal_year}</span>
            <span class="hidden md:block">₱ ${parseFloat(item.proposed_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
            <span class="hidden md:block">${item.submitted_by}</span>
            <span class="hidden md:block ${statusColor}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>
            <div class="text-right">
                <button class="view-btn bg-teal-600 text-white font-medium px-4 py-2 rounded-xl hover:bg-teal-700 transition-colors duration-200 text-sm">
                    view
                </button>
            </div>
        `;
        row.querySelector('.view-btn').addEventListener('click', () => openModal(item));
        dataList.appendChild(row);
    });
}

searchInput.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    if (searchTerm.length > 0) {
        clearSearchBtn.classList.remove('hidden');
    } else {
        clearSearchBtn.classList.add('hidden');
    }
    const filteredData = budgetPlans.filter(item => 
        Object.values(item).some(value => 
            String(value).toLowerCase().includes(searchTerm)
        )
    );
    renderData(filteredData);
});

clearSearchBtn.addEventListener('click', () => {
    searchInput.value = '';
    clearSearchBtn.classList.add('hidden');
    renderData(budgetPlans);
});

// Initial data fetch
fetchData();