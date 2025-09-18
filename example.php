<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="layout/resources/css/payable.css">
  <link rel="stylesheet" href="layout/resources/css/payable_modal.css">
</head>
<body>

<?php include 'layout/sidebar.php'; ?>
<script src="layout/resources/js/sidebar.js"></script>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow mx-auto w-full max-w-6xl">
    <header class="flex flex-col md:flex-row justify-between items-center pb-5 border-b border-gray-300 animate-fadeIn">
        <h1 class="text-2xl font-bold text-[#191970]">Payable</h1>
    </header>

    <section class="p-5">
        <div class="flex justify-center items-center">
            <div class="w-full max-w-4xl p-6 bg-white rounded-2xl shadow-xl border border-gray-200">
                <header class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                    <div class="relative w-full sm:w-1/2">
                        <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold hover:bg-gray-300 transition-colors duration-200">
                        filter
                    </button>
                </header>

                <div class="overflow-x-auto shadow-lg rounded-xl">
                    <table class="table w-full">
                        <thead>
                            <tr class="bg-primary text-primary-content">
                                <th>No#</th>
                                <th>Name</th>
                                <th>Requested Amount</th>
                                <th>Time Stamp</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="records-table-body">
                            <tr>
                                <td>1</td>
                                <td>nigga doe</td>
                                <td>$500.00</td>
                                <td>2025-08-26 10:00 AM</td>
                                <td>2025-09-01</td>
                                <td>High</td>
                                <td class="actions-cell">
                                    <button onclick="showModal()">Review</button>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
                <div id="no-records-message" class="text-center mt-8 text-gray-500 hidden">
                    <p>No records found. Please add a new record to get started.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<div id="review-modal" class="modal-backdrop">
    <div class="modal-container">
        <button class="modal-close-btn" onclick="hideModal()">&times;</button>
        <div class="modal-content">
            <div class="modal-header">
                <img src="layout/resources/images/sample.png" alt="Vendor Image" class="modal-image">
            </div>
            <div class="modal-content-details">
                <p><strong>Employee Name</strong></p>
                <p><strong>Position</strong></p>
                <p><strong>Department</strong></p>
                <p><strong>Age</strong></p>
                <p><strong>Contact number</strong></p>
                <p><strong>Email</strong></p>
                <p><strong>Gender</strong></p>
                <p><strong>Amount Requested</strong></p>
                <p><strong>Time stamp</strong></p>
                <p><strong>Due date</strong></p>
                <p><strong>Payment Method</strong></p>
                <p><strong>Priority</strong></p>
            </div>
            <div class="pdf-uploaded-btn" onmouseover="changeText(this, 'Click to Download')" onmouseout="changeText(this, 'pdf uploaded')">
                <span>pdf uploaded</span>
            </div>
            <div class="modal-actions">
                <button class="approve-btn">approve</button>
                <button class="reject-btn">reject</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showModal() {
        document.getElementById('review-modal').classList.add('is-visible');
    }
    
    function hideModal() {
        document.getElementById('review-modal').classList.remove('is-visible');
    }

    function changeText(element, text) {
        element.querySelector('span').textContent = text;
    }
</script>

</body>
</html>