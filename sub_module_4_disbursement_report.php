<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="layout/resources/css/budget_Report.css">
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <title>Document</title>
</head>
<body>

<?php include 'layout/ap_sidebar.php'; 
 /* lagay or mention nyo to lagi sa mga 
files nyo eto kasi yung sidebar same din 
 sa script since dapat magksama yan lagi 
 yung script sya yung nag addd ng functionality
 sa sidebar nyo
*/

?>
<script src="layout/resources/js/sidebar.js"></script>
<script src="layout/resources/js/sub_module_4_disbursement_report.js"></script>
<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow ">
     
     <div class="pb-5 border-b border-base-300 animate-fadeIn">
       <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
     </div>
  


    
       <section class="p-5">

       <div class="bg-gray-100 p-8 min-h-screen">
  <div class="container mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Disbursement Dashboard</h1>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Total Daily Disbursement</h2>
        <p id="dailyDisbursement" class="text-3xl font-bold text-gray-900">₱0.00</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Total Weekly Disbursement</h2>
        <p id="weeklyDisbursement" class="text-3xl font-bold text-gray-900">₱0.00</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Total Monthly Disbursement</h2>
        <p id="monthlyDisbursement" class="text-3xl font-bold text-gray-900">₱0.00</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Total Disbursement</h2>
        <p id="totalDisbursement" class="text-3xl font-bold text-gray-900">₱0.00</p>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-600 mb-4">Payments by Department</h2>
        <canvas id="departmentChart"></canvas>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-600 mb-4">Payments by Category</h2>
        <canvas id="categoryChart"></canvas>
      </div>
    </div>
  </div>
</div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

       </section>
   


<script>
lucide.createIcons();
</script>

   

      
 
   </main>
</body>
</html>