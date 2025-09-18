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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-base-100">

<?php include 'layout/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
  <div class="pb-5 border-b border-base-300 animate-fadeIn">
    <h1 class="text-2xl font-bold bg-white bg-clip-text text-[#191970]">Dashboard</h1>
  </div>
  <section class="p-5">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="text-sm font-semibold text-gray-500 mb-2">Total Revenue</div>
    <div id="total-revenue" class="text-3xl font-bold text-gray-900">₱0</div>
</div>

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="text-sm font-semibold text-gray-500 mb-2">Total Incoming Revenue</div>
    <div id="incoming-revenue" class="text-3xl font-bold text-blue-600">₱0</div>
</div>

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="text-sm font-semibold text-gray-500 mb-2">Total Disbursed</div>
    <div id="total-disbursed" class="text-3xl font-bold text-red-600">₱0</div>
</div>

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="text-sm font-semibold text-gray-500 mb-2">Total Departments</div>
    <div id="total-departments" class="text-3xl font-bold text-green-600">0</div>
</div>
</div>
  </section>
</main>
<script src="layout/resources/js/sidebar.js"></script>
<script src="layout/resources/js/Dashboard.js"></script>
</body>
</html>