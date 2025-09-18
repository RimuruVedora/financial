<?php
// Include the database connection file
include 'database/connection.php';

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted JSON data
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Validate and sanitize input
    $ticket_id = isset($data['ticket_id']) ? intval($data['ticket_id']) : 0;

    if ($ticket_id > 0) {
        // Start a transaction for atomicity
        $conn->begin_transaction();
        
        try {
            // Update the status in the ticket_receivable table to 'refund'
            $stmt1 = $conn->prepare("UPDATE ticket_receivable SET STATUS = 'refund' WHERE TICKET_ID = ?");
            $stmt1->bind_param("i", $ticket_id);
            $stmt1->execute();
            
            // Commit the transaction
            $conn->commit();

            // Send a success response
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Ticket status updated to "refund" successfully.']);
            
        } catch (mysqli_sql_exception $exception) {
            // Rollback the transaction on error
            $conn->rollback();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}

$conn->close();
?>