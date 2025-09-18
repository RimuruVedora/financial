<?php

require_once('database/connection.php');

// Set headers for JSON response
header('Content-Type: application/json');

// Handle POST request to approve a ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id']) && isset($_POST['collection_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $collection_id = $_POST['collection_id'];

    $conn->begin_transaction();

    try {
        // 1. Update status in `ticket_receivable` table to 'accept'
        $stmt_ticket = $conn->prepare("UPDATE ticket_receivable SET status = 'accept' WHERE Ticket_ID = ?");
        $stmt_ticket->bind_param("i", $ticket_id);
        $stmt_ticket->execute();

        // 2. Insert into `account_receivables` table with STATUS 'ACCEPTED'
        $stmt_account = $conn->prepare("INSERT INTO account_receivables (Collection_ID, TICKET_ID, STATUS) VALUES (?, ?, 'ACCEPTED')");
        $stmt_account->bind_param("ii", $collection_id, $ticket_id);
        $stmt_account->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Ticket approved successfully.']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $stmt_ticket->close();
    $stmt_account->close();
    $conn->close();
    exit;
}

// Handle GET request to fetch filtered data for the front end
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT tr.Ticket_ID, tr.collection_id, tr.ticket_Entry, tr.status, tr.resubmitted_Date, 
            c.name, c.revenue, c.type, c.date_received, c.time_received
            FROM ticket_receivable tr
            JOIN collections c ON tr.collection_id = c.collection_id
            WHERE tr.status IN ('reject', 'updated')";

    $result = $conn->query($sql);
    
    $data = [];
    $total_revenue = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'ticket_id' => $row['Ticket_ID'],
                'collection_id' => $row['collection_id'],
                'ticket_entry' => $row['ticket_Entry'],
                'name' => $row['name'],
                'revenue' => $row['revenue'],
                'type' => $row['type'],
                'date' => $row['date_received'],
                'time' => $row['time_received'],
                'action' => $row['status'],
                'resubmitted_date' => $row['resubmitted_Date']
            ];
            $revenue_numeric = (float) filter_var($row['revenue'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $total_revenue += $revenue_numeric;
        }
    }

    echo json_encode(['data' => $data, 'total_revenue' => '₱ ' . number_format($total_revenue, 2)]);

    $conn->close();
}
?>