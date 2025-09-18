<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-base-100">

<?php include 'layout/ar_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
  <div class="pb-5 border-b border-base-300 animate-fadeIn">
    <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
  </div>
  <section class="p-5">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 p-4">

      <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-start justify-between">
        <h3 class="text-xl font-bold text-gray-800">Total Receivable</h3>
        <p class="mt-4 text-4xl font-extrabold text-blue-600" id="total-receivable">₱0.00</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-start justify-between">
        <h3 class="text-xl font-bold text-gray-800">Total Receivable Request</h3>
        <p class="mt-4 text-4xl font-extrabold text-orange-500" id="total-receivable-request">0</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-start justify-between">
        <h3 class="text-xl font-bold text-gray-800">Total Debtor Persons</h3>
        <p class="mt-4 text-4xl font-extrabold text-green-500" id="total-debtors">0</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-start justify-between">
        <h3 class="text-xl font-bold text-gray-800">Collection Success Rate</h3>
        <p class="mt-4 text-4xl font-extrabold text-red-500" id="collection-success-rate">0%</p>
      </div>

      </div>
  </section>
</main>
<script src="layout/resources/js/sidebar.js"></script>
<script src="layout/resources/js/sub_module_1_dashboard.js"></script>

</body>
</html>