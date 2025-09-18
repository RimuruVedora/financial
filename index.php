<?php
// Main entry point for the application
// This will handle routing to different pages based on the request

session_start();

// Get the request URI and remove query parameters
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = trim($request_uri, '/');

// Define routes
$routes = [
    '' => 'login.php',
    'login' => 'login.php',
    'admin_dashboard' => 'admin_dashboard.php',
    'department_dashboard' => 'department_dashboard.php',
    'collection' => 'collection.php',
    'sub_module_1_collections_all' => 'sub_module_1_collections_all.php',
    'sub_module_1_collections_reject' => 'sub_module_1_collections_reject.php',
    'sub_module_1_dashboard' => 'sub_module_1_dashboard.php',
    'sub_module_2_Account_Receivables' => 'sub_module_2_Account_Receivables.php',
    'sub_module_2_Refund' => 'sub_module_2_Refund.php',
    'sub_module_2_account_payable_analytics' => 'sub_module_2_account_payable_analytics.php',
    'sub_module_2_payable' => 'sub_module_2_payable.php',
    'sub_module_2_reject_payable' => 'sub_module_2_reject_payable.php',
    'sub_module_3_Account_Receivable' => 'sub_module_3_Account_Receivable.php',
    'sub_module_3_Ap' => 'sub_module_3_Ap.php',
    'sub_module_3_Budget_Monitoring' => 'sub_module_3_Budget_Monitoring.php',
    'sub_module_3_Budget_planning' => 'sub_module_3_Budget_planning.php',
    'sub_module_3_Collection_Analytics' => 'sub_module_3_Collection_Analytics.php',
    'sub_module_3_Disbursement' => 'sub_module_3_Disbursement.php',
    'sub_module_3_budget_report' => 'sub_module_3_budget_report.php',
    'sub_module_3_dashboard' => 'sub_module_3_dashboard.php',
    'sub_module_4_disbursement_approval' => 'sub_module_4_disbursement_approval.php',
    'sub_module_4_disbursement_report' => 'sub_module_4_disbursement_report.php',
    'sub_module_4_disbursement_reports' => 'sub_module_4_disbursement_reports.php',
    'example' => 'example.php',
    'exa' => 'exa.html'
];

// Check if the route exists
if (array_key_exists($request_uri, $routes)) {
    $file_to_include = $routes[$request_uri];
    
    // Check if file exists
    if (file_exists($file_to_include)) {
        include $file_to_include;
    } else {
        // File not found, redirect to login
        include 'login.php';
    }
} else {
    // Route not found, redirect to login
    include 'login.php';
}
?>
