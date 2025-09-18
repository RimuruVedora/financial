<?php
// Set headers to allow cross-origin requests and to specify a JSON content type.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Use the separate connection file for database connection
require_once 'database/connection.php';

// Check if the connection was successful before proceeding
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// SQL query to join collections and ticket_receivable tables
// It filters for records where both tables have a status of 'reject' or 'updated' and sums the revenue.
$sql = "
SELECT
    SUM(c.amount) AS total_revenue
FROM
    ticket_receivable tr
INNER JOIN
    collections c ON tr.collection_id = c.collection_id
WHERE
    c.status IN ('reject', 'updated')
    AND tr.status IN ('reject', 'updated');
";

// Execute the query
$result = $conn->query($sql);

$total_revenue = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_revenue = $row['total_revenue'];
}

// Re-run the query to get the detailed list of records
$sql = "
SELECT
    tr.Ticket_ID,
    tr.collection_id,
    tr.ticket_Entry,
    c.name,
    c.amount AS revenue,
    c.remittance_for,
    c.date,
    c.time,
    c.status AS collection_status,
    tr.status AS ticket_status,
    tr.resubmitted_Date
FROM
    ticket_receivable tr
INNER JOIN
    collections c ON tr.collection_id = c.collection_id
WHERE
    c.status IN ('reject', 'updated')
    AND tr.status IN ('reject', 'updated')
ORDER BY
    c.date DESC;
";

// Execute the query for the detailed data
$result = $conn->query($sql);

$data = array();

// Fetch results and store in an array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status_to_display = $row['collection_status'];
        
        $data[] = array(
            'ticket_id' => $row['Ticket_ID'],
            'collection_id' => $row['collection_id'],
            'ticket_entry' => $row['ticket_Entry'],
            'name' => $row['name'],
            'revenue' => '₱ ' . number_format($row['revenue'], 2),
            'type' => $row['remittance_for'],
            'action' => $status_to_display,
            'date' => $row['date'],
            'time' => substr($row['time'], 0, 5),
            'resubmitted_date' => $row['resubmitted_Date']
        );
    }
}

// Close the database connection
$conn->close();

// Return both the total revenue and the detailed data
echo json_encode([
    'total_revenue' => '₱ ' . number_format($total_revenue, 2),
    'data' => $data
]);

?>