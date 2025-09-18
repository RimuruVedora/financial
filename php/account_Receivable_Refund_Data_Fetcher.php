<?php
// Include the database connection file
include 'database/connection.php';

// SQL query to fetch all required data with a status of 'ACCEPTED' or 'REFUND'
$sql = "SELECT 
            t1.TICKET_ID,
            t2.name,
            t3.ticket_Entry,
            t3.Amount AS amount,
            (t3.balance - t3.Amount) AS balance,
            t2.remittance_for,
            t2.date,
            t2.type,
            t1.STATUS AS account_receivable_status
        FROM account_receivables AS t1
        JOIN collections AS t2 ON t1.Collection_ID = t2.collection_id
        JOIN ticket_receivable AS t3 ON t1.TICKET_ID = t3.Ticket_ID
        WHERE t1.STATUS = 'REFUND' 
        GROUP BY t1.TICKET_ID";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    // Fetch data and store in an array
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close connection
$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>