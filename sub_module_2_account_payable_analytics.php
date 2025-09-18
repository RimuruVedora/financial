<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Payable Analytics</title>
  <link rel="stylesheet" href="layout/resources/css/sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
     .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
</style>
<body class="bg-base-100">

<?php include 'layout/ap_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
  <div class="pb-5 border-b border-base-300 animate-fadeIn">
    <h1 class="text-2xl font-bold">Account Payable Analytics Dashboard</h1>
    <p class="text-sm text-base-content/80 mt-1">
        Overview of key metrics and trends for accounts payable.
    </p>
  </div>
  <section class="py-6 space-y-8 animate-fadeIn">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card bg-base-200 shadow-xl border border-base-300">
            <div class="card-body p-5">
                <h2 class="card-title text-base-content/60 text-sm font-semibold">Total Payable</h2>
                <div class="flex items-center gap-2 mt-2">
                    <span id="totalPayable" class="text-2xl font-bold">...</span>
                </div>
            </div>
        </div>
        <div class="card bg-base-200 shadow-xl border border-base-300">
            <div class="card-body p-5">
                <h2 class="card-title text-base-content/60 text-sm font-semibold">Total Rejected</h2>
                <div class="flex items-center gap-2 mt-2">
                    <span id="totalRejected" class="text-2xl font-bold">...</span>
                </div>
            </div>
        </div>
        <div class="card bg-base-200 shadow-xl border border-base-300">
            <div class="card-body p-5">
                <h2 class="card-title text-base-content/60 text-sm font-semibold">Total Overdue</h2>
                <div class="flex items-center gap-2 mt-2">
                    <span id="totalOverdue" class="text-2xl font-bold">...</span>
                </div>
            </div>
        </div>
        <div class="card bg-base-200 shadow-xl border border-base-300">
            <div class="card-body p-5">
                <h2 class="card-title text-base-content/60 text-sm font-semibold">Total Payable by Spend</h2>
                <div class="flex items-center gap-2 mt-2">
                    <span id="totalPayableSpend" class="text-2xl font-bold">...</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="card bg-base-200 shadow-xl border border-base-300 col-span-1 lg:col-span-2">
            <div class="card-body">
                <h2 class="card-title text-base-content text-lg font-semibold">Invoice Processing & Late Payment Trends</h2>
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card bg-base-200 shadow-xl border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-base-content text-lg font-semibold">Invoice Exception Reasons</h2>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1">
      <div class="card bg-base-200 shadow-xl border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-base-content text-lg font-semibold">Top Vendors by Spend</h2>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
  </section>
</main>
<script src="layout/resources/js/sidebar.js"></script>
<script src="layout/resources/js/sub_module_2_analytics.js"></script>
</body>
</html>