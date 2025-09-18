<?php
// Include the database connection file
include 'database/connection.php';

// Check if the connection is successful
if ($conn->connect_error) {
    // Return a JSON error message and exit
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Handle GET request for fetching a PDF statement
if (isset($_GET['fetch_statement']) && isset($_GET['ticket_id'])) {
    $ticketId = $_GET['ticket_id'];
    $sql = "SELECT statement FROM reminders WHERE ticket_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        http_response_code(500);
        exit;
    }
    
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $stmt->bind_result($statementBlob);
    
    if ($stmt->fetch() && !empty($statementBlob)) {
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=\"statement_".$ticketId.".pdf\"");
        header("Content-Length: " . strlen($statementBlob));
        echo $statementBlob;
        
    } else {
        http_response_code(404);
        echo "Statement not found.";
    }

    $stmt->close();
    $conn->close();
    exit;
}
// Handler to fetch assignment details for a specific ticket
elseif (isset($_GET['fetch_assignment_details']) && isset($_GET['ticket_id'])) {
    header('Content-Type: application/json');
    $ticketId = $_GET['ticket_id'];
    
    $sql = "SELECT r.due_date, c.collector_name FROM reminders r LEFT JOIN collectors c ON r.collector_id = c.collector_id WHERE r.ticket_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'collector_name' => $row['collector_name'] ?? 'N/A',
            'due_date' => $row['due_date'] ?? 'N/A'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No assignment found for this ticket.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
// New endpoint to fetch full debtor details for the "View" modal
elseif (isset($_GET['fetch_debtor_details']) && isset($_GET['ticket_id'])) {
    header('Content-Type: application/json');
    $ticketId = $_GET['ticket_id'];
    
    $sql = "SELECT 
                tr.Ticket_ID,
                tr.balance,
                tr.Amount AS total_paid_amount,
                tr.date_time,
                c.name AS debtor_name,
                col.collector_name,
                r.claim_status,
                r.notification_status,
                ab.down_payment,
                ab.`30_Days`,
                ab.`60_Days`,
                ab.`90_Days`
            FROM 
                ticket_receivable tr
            LEFT JOIN 
                collections c ON tr.collection_id = c.collection_id
            LEFT JOIN 
                reminders r ON tr.Ticket_ID = r.ticket_id
            LEFT JOIN 
                collectors col ON r.collector_id = col.collector_id
            LEFT JOIN 
                aging_bucket ab ON tr.aging_id = ab.aging_id
            WHERE 
                tr.Ticket_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No details found for this ticket.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
// Handle POST request for assigning a collector
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $ticketId = $data['ticketId'] ?? null;
    $collectorId = $data['collectorId'] ?? null;
    $dueDate = $data['dueDate'] ?? null;
    
    if (!$ticketId || !is_numeric($collectorId) || !$dueDate) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing data for collector assignment.']);
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reminders (ticket_id, collector_id, due_date, notification_status, claim_status) VALUES (?, ?, ?, 'pending', 'ongoing')");
    $stmt->bind_param("iis", $ticketId, $collectorId, $dueDate);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reminder created and collector assigned successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create reminder. Error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
// Handle GET requests for DSO trend
elseif (isset($_GET['fetch_dso_trend']) && $_GET['fetch_dso_trend'] == 'true') {
    header('Content-Type: application/json');
    $sql_dso = "SELECT DATE_FORMAT(date_time, '%Y-%m') AS month, SUM(balance) AS total_receivables, SUM(Amount) AS total_sales, DAY(LAST_DAY(date_time)) AS days_in_month FROM ticket_receivable WHERE date_time >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month";
    $result_dso = $conn->query($sql_dso);

    $data_dso = [];
    while ($row = $result_dso->fetch_assoc()) {
        $dso = ($row['total_sales'] > 0) ? ($row['total_receivables'] / $row['total_sales']) * $row['days_in_month'] : 0;
        $data_dso[] = ['month' => $row['month'], 'dso' => round($dso, 2)];
    }

    echo json_encode($data_dso);
    $conn->close();
    exit;
}
// Handle GET requests for aging counts
elseif (isset($_GET['fetch_aging_counts']) && $_GET['fetch_aging_counts'] == 'true') {
    header('Content-Type: application/json');
    $sql_aging_counts = "
        SELECT
            COUNT(CASE WHEN DATEDIFF(CURDATE(), tr.date_time) <= 3 THEN 1 END) AS current,
            COUNT(CASE WHEN DATEDIFF(CURDATE(), tr.date_time) > 3 AND DATEDIFF(CURDATE(), tr.date_time) <= 33 THEN 1 END) AS thirty_days,
            COUNT(CASE WHEN DATEDIFF(CURDATE(), tr.date_time) > 33 AND DATEDIFF(CURDATE(), tr.date_time) <= 63 THEN 1 END) AS sixty_days,
            COUNT(CASE WHEN DATEDIFF(CURDATE(), tr.date_time) > 63 THEN 1 END) AS ninety_plus_days
        FROM
            ticket_receivable tr
        WHERE
            tr.balance > 0
    ";

    $result_aging_counts = $conn->query($sql_aging_counts);

    if ($result_aging_counts->num_rows > 0) {
        $counts = $result_aging_counts->fetch_assoc();
        echo json_encode($counts);
    } else {
        echo json_encode(['current' => 0, 'thirty_days' => 0, 'sixty_days' => 0, 'ninety_plus_days' => 0]);
    }
    $conn->close();
    exit;
}
// Handle GET requests for collectors
elseif (isset($_GET['fetch_collectors']) && $_GET['fetch_collectors'] == 'true') {
    header('Content-Type: application/json');
    // FIX: Corrected the typo 'avaialble' to 'available' in the SQL query.
    $sql_collectors = "SELECT collector_id, collector_name FROM collectors WHERE status = 'available'";
    $result_collectors = $conn->query($sql_collectors);

    $collectors = array();
    if ($result_collectors->num_rows > 0) {
        while($row = $result_collectors->fetch_assoc()) {
            $collectors[] = $row;
        }
    }
    echo json_encode($collectors);
    $conn->close();
    exit;
}
// Default case to handle the main table data fetch
else {
    header('Content-Type: application/json');
    $sql = "SELECT 
                tr.Ticket_ID,
                c.name AS customer_name,
                tr.ticket_Entry,
                tr.balance AS total_due,
                tr.date_time AS last_payment_date,
                ab.down_payment AS total_current_amount,
                ab.`30_Days` AS total_thirty_days,
                ab.`60_Days` AS total_sixty_days,
                ab.`90_Days` AS total_ninety_days,
                col.collector_name AS assigned_collector
                
            FROM 
                ticket_receivable tr
            LEFT JOIN 
                collections c ON tr.collection_id = c.collection_id
            LEFT JOIN 
                aging_bucket ab ON tr.aging_id = ab.aging_id
            LEFT JOIN 
                reminders r ON tr.Ticket_ID = r.ticket_id
            LEFT JOIN 
                collectors col ON r.collector_id = col.collector_id
            WHERE 
                tr.balance > 0";
        
    $result = $conn->query($sql);
    
    $data = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
    $conn->close();
    exit;
}
?>