<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="layout/resources/css/budget_Report.css">
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <title>Document</title>
</head>
<body>

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
       <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
     </div>
  


    
       <section class="p-5">

       <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Budget Report</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between space-y-4 md:space-y-0 md:space-x-4">
                <input type="text" id="searchInput" placeholder="Search by Report Ticket, Department..." class="flex-1 w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <div class="flex flex-wrap space-x-2">
                    <button id="searchButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">Search</button>
                    <button id="filterButton" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">Filter</button>
                </div>
            </div>
            
            <div id="filterOptions" class="mt-4 hidden space-y-4">
                <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700">Date From:</label>
                    <input type="date" id="dateFrom" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700">Date To:</label>
                    <input type="date" id="dateTo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="reportTableBody" class="bg-white divide-y divide-gray-200">
                    </tbody>
            </table>
        </div>

        <div id="loadingOverlay" class="loading-overlay">
            <div class="loader"></div>
        </div>

        <div id="modalOverlay" class="modal-overlay">
            <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 md:w-2/3 lg:w-1/2 max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Report Details</h2>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>
                <div id="modalContent" class="space-y-4">
                    </div>
            </div>
        </div>
    </div>

    <script src="layout/resources/js/Sub_module_3_budget_Report.js"></script>
       </section>   
 
   </main>
</body>
</html>