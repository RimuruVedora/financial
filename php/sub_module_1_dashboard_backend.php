<?php

header('Content-Type: application/json');

// Include the database connection file
require_once 'database/connection.php';

// Initialize a data array to hold all the results
$data = [
    'total_receivable' => 0,
    'total_receivable_requests' => 0,
    'total_debtor_persons' => 0,
    'collection_success_rate' => 0
];

// 1. Calculate Total Receivable (Incoming revenue from collections)
// Sum of 'amount' from collections table where status is 'approved'
$sql_total_receivable = "SELECT SUM(amount) AS total_receivable FROM collections WHERE status = 'approved'";
$result_total_receivable = $conn->query($sql_total_receivable);
if ($result_total_receivable && $result_total_receivable->num_rows > 0) {
    $row = $result_total_receivable->fetch_assoc();
    $data['total_receivable'] = $row['total_receivable'] ?? 0;
}

// 2. Calculate Total Receivable Requests (Total pending from collections)
// Count of entries in collections table where status is 'pending'
$sql_total_pending = "SELECT COUNT(*) AS total_pending FROM collections WHERE status = 'pending'";
$result_total_pending = $conn->query($sql_total_pending);
if ($result_total_pending && $result_total_pending->num_rows > 0) {
    $row = $result_total_pending->fetch_assoc();
    $data['total_receivable_requests'] = $row['total_pending'] ?? 0;
}

// 3. Calculate Total Debtor Persons
// Count distinct persons from 'collections' who have a balance from 'ticket_receivable' and are in 'aging_bucket'
// and their balance is > 0
$sql_total_debtors = "
    SELECT COUNT(DISTINCT c.name) AS total_debtors
    FROM collections c
    JOIN ticket_receivable tr ON c.collection_id = tr.collection_id
    JOIN aging_bucket ab ON ab.collection_id = c.collection_id
    WHERE tr.balance > 0";
$result_total_debtors = $conn->query($sql_total_debtors);
if ($result_total_debtors && $result_total_debtors->num_rows > 0) {
    $row = $result_total_debtors->fetch_assoc();
    $data['total_debtor_persons'] = $row['total_debtors'] ?? 0;
}

// 4. Calculate Collection Success Rate
// (Count of 'approved' / (Count of 'approved' + Count of 'rejected')) * 100
$sql_success_rate = "
    SELECT
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM collections";
$result_success_rate = $conn->query($sql_success_rate);

if ($result_success_rate && $result_success_rate->num_rows > 0) {
    $row = $result_success_rate->fetch_assoc();
    $approved_count = $row['approved_count'];
    $rejected_count = $row['rejected_count'];
    $total_collections = $approved_count + $rejected_count;

    if ($total_collections > 0) {
        $data['collection_success_rate'] = ($approved_count / $total_collections) * 100;
    }
}

// Return the data as a JSON response
echo json_encode($data);

// Close the connection
$conn->close();

?>