<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget Planning</title>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f4f6;
    }
    /* This custom class will force the backdrop to be semi-transparent */
    .modal-backdrop-custom {
      background-color: rgba(0, 0, 0, 0.5) !important;
    }

    @tailwind base;
@tailwind components;
@tailwind utilities;
  </style>
 
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'layout/sidebar.php'; 
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
        <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Budget Planning Dashboard</h1>
      </div>
      
      <section class="p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
            <div class="bg-teal-600 text-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center justify-center min-h-[140px] transition-all duration-300 ease-in-out transform hover:scale-105">
                <span class="text-xl sm:text-2xl font-medium">Total Proposed Budget</span>
                <span id="totalProposedBudget" class="text-3xl sm:text-4xl font-bold mt-2">₱ 0.00</span>
            </div>
            <div class="bg-teal-600 text-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center justify-center min-h-[140px] transition-all duration-300 ease-in-out transform hover:scale-105">
                <span class="text-xl sm:text-2xl font-medium">Planned By Departments</span>
                <span id="departmentsCount" class="text-3xl sm:text-4xl font-bold mt-2">0 departments</span>
            </div>
            <div class="bg-teal-600 text-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center justify-center min-h-[140px] transition-all duration-300 ease-in-out transform hover:scale-105">
                <span class="text-xl sm:text-2xl font-medium">Pending Plans</span>
                <span id="pendingPlansCount" class="text-3xl sm:text-4xl font-bold mt-2">0 plans</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
            <div class="relative flex items-center flex-grow">
                <input type="text" id="searchInput" placeholder="Search..."
                       class="w-full pl-10 pr-8 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-transparent transition-all duration-200">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button id="clearSearch" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hidden hover:text-gray-600 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <button class="bg-gray-200 text-gray-600 p-3 rounded-xl hover:bg-gray-300 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM6 12a2 2 0 11-4 0 2 2 0 014 0zM18 12a2 2 0 11-4 0 2 2 0 014 0zM10 18a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 font-bold text-gray-700 border-b border-gray-200 pb-4 mb-4">
                <span>Department</span>
                <span class="hidden md:block">Fiscal Year</span>
                <span class="hidden md:block">Proposed Amount</span>
                <span class="hidden md:block">Submitted By</span>
                <span class="hidden md:block">Status</span>
                <span class="text-right">Action</span>
            </div>
            
            <div id="dataList">
                </div>
            <div id="loadingIndicator" class="text-center text-gray-500 mt-4 hidden">Loading...</div>
        </div>
      </section>
      
      <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 modal-backdrop-custom" onclick="closeModal()"></div>
        <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-lg relative border border-gray-300 overflow-y-auto max-h-[90vh]">
            <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Department</label>
                    <input type="text" id="modalDepartment" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Submitted By</label>
                    <input type="text" id="modalSubmittedBy" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Proposed Amount</label>
                    <input type="text" id="modalProposedAmount" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Fiscal Year</label>
                    <input type="text" id="modalFiscalYear" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Justification</label>
                    <textarea id="modalJustification" rows="4" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Attached File</label>
                    <input type="text" id="modalAttachedFile" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly>
                </div>
                 <div id="rejectionReasonContainer" class="hidden">
                    <label class="block text-gray-700 font-bold mb-1">Rejection Reason</label>
                    <textarea id="modalRejectionReason" rows="2" class="w-full p-3 rounded-xl border border-gray-300 bg-gray-100" readonly></textarea>
                </div>
            </div>

            <div id="actionButtons" class="flex flex-col sm:flex-row justify-between mt-6 space-y-4 sm:space-y-0 sm:space-x-4">
                <button id="approveBtn" class="flex-1 bg-green-500 text-white font-medium py-3 rounded-xl hover:bg-green-600 transition-colors duration-200">
                    Approve
                </button>
                <button id="rejectBtn" class="flex-1 bg-red-500 text-white font-medium py-3 rounded-xl hover:bg-red-600 transition-colors duration-200">
                    Reject
                </button>
            </div>
             <div id="noActionMessage" class="hidden text-center text-gray-500 font-bold mt-6">
                Status is already finalized.
            </div>
        </div>
      </div>
      
      <div id="approveModal" class="fixed inset-0 z-[60] flex items-center justify-center hidden">
          <div class="absolute inset-0 modal-backdrop-custom"></div>
          <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-sm relative border border-gray-300">
              <h2 class="text-xl font-bold mb-4">Confirm Approval</h2>
              <p class="text-gray-700 mb-6">Are you sure you want to approve this budget plan?</p>
              <div class="flex justify-end space-x-4">
                  <button id="cancelApproveBtn" class="bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-xl hover:bg-gray-400 transition-colors">
                      No
                  </button>
                  <button id="confirmApproveBtn" class="bg-green-500 text-white font-medium py-2 px-4 rounded-xl hover:bg-green-600 transition-colors">
                      Yes
                  </button>
              </div>
          </div>
      </div>

      <div id="rejectModal" class="fixed inset-0 z-[60] flex items-center justify-center hidden">
          <div class="absolute inset-0 modal-backdrop-custom"></div>
          <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-sm relative border border-gray-300">
              <h2 class="text-xl font-bold mb-4">Reject Budget Plan</h2>
              <p class="text-gray-700 mb-4">Please provide a reason for rejection.</p>
              <textarea id="rejectReasonInput" rows="4" class="w-full p-3 rounded-xl border border-gray-300 mb-6 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
              <div class="flex justify-end space-x-4">
                  <button id="cancelRejectBtn" class="bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-xl hover:bg-gray-400 transition-colors">
                      Cancel
                  </button>
                  <button id="submitRejectBtn" class="bg-red-500 text-white font-medium py-2 px-4 rounded-xl hover:bg-red-600 transition-colors">
                      Submit
                  </button>
              </div>
          </div>
      </div>
      
      <div id="successModal" class="fixed inset-0 z-[70] flex items-center justify-center hidden">
          <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-xs relative text-center border border-gray-300 transform transition-transform duration-300 scale-95 opacity-0" id="successModalContent">
              <h3 class="text-lg font-bold text-gray-800 mb-2">Success!</h3>
              <p id="successMessage" class="text-gray-600"></p>
          </div>
      </div>

      <script>
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
        const successModal = document.getElementById('successModal');
        const successModalContent = document.getElementById('successModalContent');
        const successMessage = document.getElementById('successMessage');

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
        
        // New function to open the success modal
        function openSuccessModal(message) {
            successMessage.textContent = message;
            successModal.classList.remove('hidden');
            // Animate the modal in
            setTimeout(() => {
                successModalContent.classList.remove('scale-95', 'opacity-0');
                successModalContent.classList.add('scale-100', 'opacity-100');
            }, 10); // Small delay to allow 'hidden' class to be removed first
            
            // Automatically close the modal after 3 seconds
            setTimeout(() => {
                successModalContent.classList.remove('scale-100', 'opacity-100');
                successModalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    successModal.classList.add('hidden');
                }, 300); // Wait for the transition to finish before hiding
            }, 3000);
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
                    const errorText = await response.text();
                    throw new Error(`Network response was not ok: ${response.status} ${response.statusText} - ${errorText}`);
                }

                const result = await response.json();
                if (result.success) {
                    closeModal();
                    closeApproveModal();
                    closeRejectModal();
                    
                    // Show a success message based on the status
                    if (status === 'approved') {
                        openSuccessModal('Budget plan approved successfully!');
                    } else if (status === 'rejected') {
                        openSuccessModal('Budget plan rejected successfully!');
                    }
                    
                    fetchData(); // Refresh the data
                } else {
                    console.error('Failed to update status:', result.error);
                    alert('Failed to update status. Please try again.');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('An error occurred. Please check your connection and try again.');
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
      </script>
    </main>
  </div>
</body>
</html>