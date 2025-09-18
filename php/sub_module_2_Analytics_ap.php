<?php

header('Content-Type: application/json');

include_once 'database/connection.php';

// Function to fetch data and handle errors
function fetchData($conn, $query) {
    $result = $conn->query($query);
    if ($result) {
        return $result;
    } else {
        // Log the error for debugging purposes
        error_log("Database query error: " . $conn->error);
        return null;
    }
}

// 1. Total Payable people by adding up the employee and vendors that has "pending" status
$query_employee_pending = "SELECT COUNT(*) FROM payable_employee WHERE Payable_Status = 'PENDING'";
$query_vendors_pending = "SELECT COUNT(*) FROM payable_vendors WHERE Payable_Status = 'PENDING'";

$result_employee = fetchData($conn, $query_employee_pending);
$result_vendors = fetchData($conn, $query_vendors_pending);

$totalPayable = 0;
if ($result_employee && $result_employee->num_rows > 0) {
    $totalPayable += $result_employee->fetch_row()[0];
}
if ($result_vendors && $result_vendors->num_rows > 0) {
    $totalPayable += $result_vendors->fetch_row()[0];
}

// 2. Total Rejected from ticket_payable status "reject"
$query_rejected = "SELECT COUNT(*) FROM ticket_payable WHERE status = 'reject'";
$result_rejected = fetchData($conn, $query_rejected);
$totalRejected = ($result_rejected && $result_rejected->num_rows > 0) ? $result_rejected->fetch_row()[0] : 0;

// 3. Total Overdue
$query_employee_overdue = "SELECT COUNT(*) FROM payable_employee WHERE Payable_Status = 'PENDING' AND due_date < CURDATE()";
$query_vendors_overdue = "SELECT COUNT(*) FROM payable_vendors WHERE Payable_Status = 'PENDING' AND due_date < CURDATE()";

$result_employee_overdue = fetchData($conn, $query_employee_overdue);
$result_vendors_overdue = fetchData($conn, $query_vendors_overdue);

$totalOverdue = 0;
if ($result_employee_overdue && $result_employee_overdue->num_rows > 0) {
    $totalOverdue += $result_employee_overdue->fetch_row()[0];
}
if ($result_vendors_overdue && $result_vendors_overdue->num_rows > 0) {
    $totalOverdue += $result_vendors_overdue->fetch_row()[0];
}

// 4. Total Payable by Spend
// Note: `payable_vendors` uses `Request_Amount` while `payable_employee` uses `Requested_Amount`
$query_employee_spend = "SELECT SUM(Requested_Amount) FROM payable_employee WHERE Payable_Status = 'PENDING'";
$query_vendors_spend = "SELECT SUM(Request_Amount) FROM payable_vendors WHERE Payable_Status = 'PENDING'";

$result_employee_spend = fetchData($conn, $query_employee_spend);
$result_vendors_spend = fetchData($conn, $query_vendors_spend);

$totalPayableSpend = 0;
if ($result_employee_spend && $result_employee_spend->num_rows > 0) {
    $totalPayableSpend += $result_employee_spend->fetch_row()[0];
}
if ($result_vendors_spend && $result_vendors_spend->num_rows > 0) {
    $totalPayableSpend += $result_vendors_spend->fetch_row()[0];
}

// 5. Invoice Processing & Late Payment Trends
$invoiceTrendsData = [];
$query_employee_trends = "SELECT DATE(date_time) AS date, COUNT(*) AS total_invoices, SUM(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) AS late_invoices FROM payable_employee GROUP BY date ORDER BY date";
$query_vendors_trends = "SELECT DATE(date_time) AS date, COUNT(*) AS total_invoices, SUM(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) AS late_invoices FROM payable_vendors GROUP BY date ORDER BY date";

$result_employee_trends = fetchData($conn, $query_employee_trends);
$result_vendors_trends = fetchData($conn, $query_vendors_trends);

// Combine and process the results
$trends = [];
if ($result_employee_trends && $result_employee_trends->num_rows > 0) {
    while ($row = $result_employee_trends->fetch_assoc()) {
        $trends[$row['date']]['total_invoices'] = ($trends[$row['date']]['total_invoices'] ?? 0) + $row['total_invoices'];
        $trends[$row['date']]['late_invoices'] = ($trends[$row['date']]['late_invoices'] ?? 0) + $row['late_invoices'];
    }
}
if ($result_vendors_trends && $result_vendors_trends->num_rows > 0) {
    while ($row = $result_vendors_trends->fetch_assoc()) {
        $trends[$row['date']]['total_invoices'] = ($trends[$row['date']]['total_invoices'] ?? 0) + $row['total_invoices'];
        $trends[$row['date']]['late_invoices'] = ($trends[$row['date']]['late_invoices'] ?? 0) + $row['late_invoices'];
    }
}
ksort($trends); // Sort by date
foreach ($trends as $date => $data) {
    $invoiceTrendsData['labels'][] = $date;
    $invoiceTrendsData['total'][] = $data['total_invoices'];
    $invoiceTrendsData['late'][] = $data['late_invoices'];
}

// 6. Invoice Exception Reasons (Updated query)
$query_reasons = "SELECT REASON, COUNT(*) AS count FROM ticket_payable WHERE status = 'reject' GROUP BY REASON";
$result_reasons = fetchData($conn, $query_reasons);
$exceptionReasonsData = ['labels' => [], 'data' => []];
if ($result_reasons && $result_reasons->num_rows > 0) {
    while ($row = $result_reasons->fetch_assoc()) {
        $exceptionReasonsData['labels'][] = $row['REASON'];
        $exceptionReasonsData['data'][] = $row['count'];
    }
}

// 7. Top Vendors by Spend
$query_vendors_spend_chart = "SELECT Company_Name, SUM(Request_Amount) AS total_spend FROM payable_vendors WHERE Payable_Status = 'PENDING' GROUP BY Company_Name ORDER BY total_spend DESC LIMIT 5";
$result_vendors_spend_chart = fetchData($conn, $query_vendors_spend_chart);
$topVendorsData = ['labels' => [], 'data' => []];
if ($result_vendors_spend_chart && $result_vendors_spend_chart->num_rows > 0) {
    while ($row = $result_vendors_spend_chart->fetch_assoc()) {
        $topVendorsData['labels'][] = $row['Company_Name'];
        $topVendorsData['data'][] = $row['total_spend'];
    }
}

$conn->close();

// Prepare the final JSON response
$response = [
    "totalPayable" => $totalPayable,
    "totalRejected" => $totalRejected,
    "totalOverdue" => $totalOverdue,
    "totalPayableSpend" => $totalPayableSpend,
    "invoiceTrendsData" => $invoiceTrendsData,
    "exceptionReasonsData" => $exceptionReasonsData,
    "topVendorsData" => $topVendorsData
];

echo json_encode($response);

?>