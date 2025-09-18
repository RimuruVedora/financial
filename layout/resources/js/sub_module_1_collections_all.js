
const tableBody = document.getElementById('table-body');
const modal = document.getElementById('modal');
const confirmationModal = document.getElementById('confirmation-modal');
const alertModal = document.getElementById('alert-modal');
const rejectModal = document.getElementById('reject-modal');

const modalName = document.getElementById('modal-name');
const modalAmount = document.getElementById('modal-amount');
const modalRemittance = document.getElementById('modal-remittance');
const modalDate = document.getElementById('modal-date');
const modalTime = document.getElementById('modal-time');
const modalType = document.getElementById('modal-type');
const modalORNo = document.getElementById('modal-or-no');
const modalLocation = document.getElementById('modal-location');

const rejectModalName = document.getElementById('reject-modal-name');
const rejectModalORNo = document.getElementById('reject-modal-or-no');
const rejectModalLocation = document.getElementById('reject-modal-location');
const rejectionReasonInput = document.getElementById('rejection-reason');


const confirmationName = document.getElementById('confirmation-name');
const confirmationAmount = document.getElementById('confirmation-amount');
const confirmationRemittance = document.getElementById('confirmation-remittance');
const confirmationDate = document.getElementById('confirmation-date');
const confirmationTime = document.getElementById('confirmation-time');
const confirmationType = document.getElementById('confirmation-type');
const ticketEntrySpan = document.getElementById('ticket-entry');

const pdfBtn = document.getElementById('pdf-btn');
const approveBtn = document.getElementById('approve-btn');
const rejectBtn = document.getElementById('reject-btn');
const confirmationAcceptBtn = document.getElementById('confirmation-accept-btn');
const confirmRejectBtn = document.getElementById('confirm-reject-btn');

const totalRequestsSpan = document.getElementById('total-requests');
const totalRevenueSpan = document.getElementById('total-revenue');

const searchInput = document.getElementById('search-input');
let allPendingRequests = [];
let currentCollectionData = null;

function showModal(data) {
    modal.classList.add('is-visible');
    modalName.textContent = `Name: ${data.name}`;
    modalORNo.textContent = `OR No: ${data.OR_NO || 'N/A'}`;
    modalLocation.textContent = `Location: ${data.LOCATION || 'N/A'}`;
    modalAmount.textContent = `Amount: ₱ ${data.amount}`;
    modalRemittance.textContent = `Remittance for: ${data.remittance_for}`;
    modalDate.textContent = `Date: ${data.date}`;
    modalTime.textContent = `Time: ${data.time}`;
    modalType.textContent = `Payment Method: ${data.type}`;
    
    approveBtn.dataset.id = data.collection_id;
    rejectBtn.dataset.id = data.collection_id;
    
    pdfBtn.innerHTML = `<span><a href="${data.pdf}" target="_blank">pdf uploaded</a></span>`;
    pdfBtn.onmouseover = () => { pdfBtn.innerHTML = `<span><a href="${data.pdf}" target="_blank">Click to Download</a></span>`; };
    pdfBtn.onmouseout = () => { pdfBtn.innerHTML = `<span><a href="${data.pdf}" target="_blank">pdf uploaded</a></span>`; };
    
    currentCollectionData = data;
}

function showRejectModal() {
    if (!currentCollectionData) return;
    modal.classList.remove('is-visible');
    rejectModal.classList.add('is-visible');
    rejectModalName.textContent = `Name: ${currentCollectionData.name}`;
    rejectModalORNo.textContent = `OR No: ${currentCollectionData.OR_NO || 'N/A'}`;
    rejectModalLocation.textContent = `Location: ${currentCollectionData.LOCATION || 'N/A'}`;
    rejectionReasonInput.value = '';
    confirmRejectBtn.dataset.id = currentCollectionData.collection_id;
}

function showConfirmationModal(data, ticketEntry) {
    confirmationModal.classList.add('is-visible');
    confirmationName.textContent = `Name: ${data.name}`;
    confirmationAmount.textContent = `Amount: ₱ ${data.amount}`;
    confirmationRemittance.textContent = `Remittance for: ${data.remittance_for}`;
    confirmationDate.textContent = `Date: ${data.date}`;
    confirmationTime.textContent = `Time: ${data.time}`;
    confirmationType.textContent = `Type: ${data.type}`;
    ticketEntrySpan.textContent = ticketEntry;
}

function showAlertModal(message) {
    document.getElementById('alert-message').textContent = message;
    alertModal.classList.add('is-visible');
}

async function fetchPendingRequests() {
    try {
        const response = await fetch('php/display_pending_request.php');
        const result = await response.json();
        if (result.success) {
            allPendingRequests = result.data;
            renderTable(allPendingRequests);
            updateStats(allPendingRequests);
        } else {
            tableBody.innerHTML = `<div class="text-center py-4 text-gray-500">No pending requests.</div>`;
            updateStats([]);
        }
    } catch (error) {
        showAlertModal('Failed to fetch data from the server. Please try again later.');
    }
}

function renderTable(requests) {
    tableBody.innerHTML = '';
    if (requests.length === 0) {
        tableBody.innerHTML = `<div class="text-center py-4 text-gray-500">No pending requests found.</div>`;
        return;
    }
    requests.forEach(request => {
        const row = document.createElement('div');
        row.className = 'grid grid-cols-5 md:grid-cols-5 gap-4 items-center bg-gray-300 p-4 rounded-3xl border border-gray-400';
        row.innerHTML = `
            <div class="p-2">${request.name}</div>
            <div class="p-2">₱ ${request.amount}</div>
            <div class="p-2">${request.type}</div>
            <div class="p-2">${request.date}</div>
            <div class="flex justify-center">
                <button class="view-btn px-6 py-2 bg-lime-500 text-white font-semibold rounded-full hover:bg-lime-600 transition-colors" data-id="${request.collection_id}">view</button>
            </div>
        `;
        tableBody.appendChild(row);
        const viewBtn = row.querySelector('.view-btn');
        viewBtn.addEventListener('click', () => showModal(request));
    });
}

function updateStats(requests) {
    const total = requests.length;
    const revenue = requests.reduce((sum, request) => sum + parseFloat(request.amount), 0);
    totalRequestsSpan.textContent = total;
    totalRevenueSpan.textContent = `₱ ${revenue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

async function handleAction(collectionId, action, reason) {
    try {
        const response = await fetch('php/collections_handle_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ collection_id: collectionId, action: action, reason: reason })
        });
        const result = await response.json();
        if (result.success) {
            const originalRequest = allPendingRequests.find(req => req.collection_id == collectionId);
            if (action === 'approve') {
                if (originalRequest) {
                    showConfirmationModal(originalRequest, result.ticket_entry);
                }
            } else if (action === 'reject') {
                showAlertModal("Rejection successful.");
                rejectModal.classList.remove('is-visible');
            }
            fetchPendingRequests();
        } else {
            showAlertModal(result.message);
        }
    } catch (error) {
        showAlertModal('An error occurred while processing the request. Please check the backend script.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchPendingRequests();
    
    document.getElementById('close-modal-btn').addEventListener('click', () => modal.classList.remove('is-visible'));
    document.getElementById('close-confirmation-modal-btn').addEventListener('click', () => confirmationModal.classList.remove('is-visible'));
    document.getElementById('close-alert-btn').addEventListener('click', () => alertModal.classList.remove('is-visible'));
    document.getElementById('close-reject-modal-btn').addEventListener('click', () => rejectModal.classList.remove('is-visible'));
    confirmationAcceptBtn.addEventListener('click', () => confirmationModal.classList.remove('is-visible'));
    
    approveBtn.addEventListener('click', () => handleAction(approveBtn.dataset.id, 'approve'));
    
    rejectBtn.addEventListener('click', () => {
        showRejectModal();
    });
    
    confirmRejectBtn.addEventListener('click', () => {
        const reason = rejectionReasonInput.value.trim();
        if (reason) {
            handleAction(confirmRejectBtn.dataset.id, 'reject', reason);
        } else {
            showAlertModal("Please provide a reason for rejection.");
        }
    });
    
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filteredRequests = allPendingRequests.filter(request => 
            request.name.toLowerCase().includes(query) ||
            request.type.toLowerCase().includes(query)
        );
        renderTable(filteredRequests);
    });
});