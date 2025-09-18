<?php

header('Content-Type: application/json');

// Include the database connection file.
include_once 'database/connection.php';

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.',
    'data' => []
];

// Use the existing connection from connection.php
if ($conn->connect_error) {
    $response['message'] = "Database Connection Error: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}


// Handle POST request for approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $payable_id = filter_var($_POST['payable_id'], FILTER_SANITIZE_NUMBER_INT);
    $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
    $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);
    // Get the rejection reason
    $reason = isset($_POST['reason']) ? filter_var($_POST['reason'], FILTER_SANITIZE_STRING) : null;

    if (empty($payable_id) || empty($type) || ($action !== 'approve' && $action !== 'reject')) {
        $response['message'] = 'Invalid request parameters.';
        echo json_encode($response);
        exit();
    }
    
    // Check if rejection action has a reason, now a more robust check
    if ($action === 'reject' && empty(trim($reason))) {
        $response['message'] = 'Reason for rejection is required.';
        echo json_encode($response);
        exit();
    }

    $conn->begin_transaction();

    try {
        $new_status = ($action === 'approve') ? 'APPROVED' : 'REJECTED';
        $ticket_status = ($action === 'approve') ? 'accept' : 'reject';
        $ticket_reason = ($action === 'reject') ? $reason : null;

        // 1. Update the payable status
        if ($type === 'employee') {
            $table = 'payable_employee';
            $id_column = 'Employee_ID';
        } else {
            $table = 'payable_vendors';
            $id_column = 'Vendor_ID';
        }

        $update_sql = "UPDATE `$table` SET `payable_Status` = ? WHERE `$id_column` = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param('si', $new_status, $payable_id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows === 0) {
            throw new Exception('No records were updated.');
        }

        // 2. Update the date_time if the action is 'approve'
        if ($action === 'approve') {
            $update_date_time_sql = "UPDATE `$table` SET `date_time` = CURRENT_TIMESTAMP() WHERE `$id_column` = ?";
            $stmt_date_time = $conn->prepare($update_date_time_sql);
            $stmt_date_time->bind_param('i', $payable_id);
            $stmt_date_time->execute();

            if ($stmt_date_time->affected_rows === 0) {
                throw new Exception('Failed to update date_time.');
            }
            $stmt_date_time->close();
        }

        // 3. Generate a unique ticket entry
        $ticket_entry = uniqid('TICKET-');
        
        // 4. Insert into ticket_payable
        $insert_sql = "INSERT INTO `ticket_payable` (`payable_ID`, `Vendor_ID`, `Employee_ID`, `ticket_Entry`, `status`, `REASON`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        
        if ($stmt_insert === false) {
            throw new Exception('Prepare statement failed: ' . $conn->error);
        }

        // Determine which ID to use and bind the parameters
        $vendor_id = ($type === 'vendor') ? $payable_id : null;
        $employee_id = ($type === 'employee') ? $payable_id : null;

        // Corrected bind_param: iissss
        $stmt_insert->bind_param('iissss', $payable_id, $vendor_id, $employee_id, $ticket_entry, $ticket_status, $ticket_reason);
        
        $stmt_insert->execute();

        if ($stmt_insert->affected_rows === 0) {
            throw new Exception('Failed to insert ticket entry.');
        }

        $conn->commit();
        $response['status'] = 'success';
        $response['message'] = 'Action completed successfully.';
        $response['ticket_entry'] = $ticket_entry;

    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Transaction failed: ' . $e->getMessage();
    }

    echo json_encode($response);
    exit();
}
// ... (rest of the file remains the same)
try {
    // Helper function to calculate allocation status
    function getAllocationStatus($allocated, $spent, $requested) {
        $newSpent = $spent + $requested;
        $status = 'N/A';
        $color = 'bg-gray-500';

        if ($allocated !== null) {
            $half = $allocated * 0.5;
            $threeQuarters = $allocated * 0.75;

            if ($newSpent > $allocated) {
                $status = 'Overused';
                $color = 'bg-red-500';
            } elseif ($newSpent == $allocated) {
                $status = 'Limit';
                $color = 'bg-red-500';
            } elseif ($newSpent > $threeQuarters) {
                $status = 'Nearly Limit';
                $color = 'bg-orange-500';
            } elseif ($newSpent > $half) {
                $status = 'Intermediate';
                $color = 'bg-yellow-500';
            } else {
                $status = 'Good';
                $color = 'bg-green-500';
            }
        }
        return ['status' => $status, 'color' => $color];
    }

    // Fetch pending payable employees, including new fields and department budget
    $employee_query = "
        SELECT 
            pe.`Employee_ID`, 
            pe.`First_Name`, 
            pe.`Middle_Name`, 
            pe.`Last_Name`, 
            pe.`Job_Tittle`, 
            pe.`Requested_Amount`, 
            pe.`Payable_Status`, 
            pe.`Due_Date`, 
            pe.`priority`,
            pe.`Email`,
            pe.`Address`,
            pe.`payment_method`,
            pe.`justification`,
            pe.`Gender`,
            pe.`Age`,
            pe.`Document`,
            pe.`profile_picture`,
            d.`department`,
            db.`allocated_amount`,
            db.`spent_amount`
        FROM `payable_employee` pe
        LEFT JOIN `department` d ON pe.`department_id` = d.`department_id`
        LEFT JOIN `department_budget` db ON pe.`department_id` = db.`department_id`
        WHERE pe.`Payable_Status` = 'PENDING'
    ";
    $employee_result = $conn->query($employee_query);
    $employee_payables = [];
    if ($employee_result) {
        while ($row = $employee_result->fetch_assoc()) {
            if ($row['Document']) {
                $row['Document'] = base64_encode($row['Document']);
            }
            if ($row['profile_picture']) {
                $row['profile_picture'] = base64_encode($row['profile_picture']);
            }
            $row['allocation_status'] = getAllocationStatus($row['allocated_amount'], $row['spent_amount'], $row['Requested_Amount']);
            $employee_payables[] = $row;
        }
    }

    // Fetch pending payable vendors, including new fields and department budget
    $vendor_query = "
        SELECT 
            pv.`Vendor_ID`, 
            pv.`Company_Name`, 
            pv.`Request_Amount`, 
            pv.`Due_Date`, 
            pv.`priority`,
            pv.`First_Name`,
            pv.`Last_Name`,
            pv.`contact_number`,
            pv.`Email`,
            pv.`Address`,
            pv.`payment_method`,
            pv.`purpose`,
            pv.`Document`,
            pv.`profile_picture`,
            pv.`department_id`,
            db.`allocated_amount`,
            db.`spent_amount`
        FROM `payable_vendors` pv
        LEFT JOIN `department_budget` db ON pv.`department_id` = db.`department_id`
        WHERE pv.`Payable_Status` = 'PENDING'
    ";
    $vendor_result = $conn->query($vendor_query);
    $vendor_payables = [];
    if ($vendor_result) {
        while ($row = $vendor_result->fetch_assoc()) {
            if ($row['Document']) {
                $row['Document'] = base64_encode($row['Document']);
            }
            if ($row['profile_picture']) {
                $row['profile_picture'] = base64_encode($row['profile_picture']);
            }
            $row['allocation_status'] = getAllocationStatus($row['allocated_amount'], $row['spent_amount'], $row['Request_Amount']);
            $vendor_payables[] = $row;
        }
    }

    // Calculate total employee payable cost and requests
    $employee_cost_query = "SELECT SUM(`Requested_Amount`) AS total_employee_cost, COUNT(`Employee_ID`) AS total_employee_requests FROM `payable_employee` WHERE `Payable_Status` = 'PENDING'";
    $employee_cost_result = $conn->query($employee_cost_query);
    $employee_summary = $employee_cost_result->fetch_assoc();

    // Calculate total vendor payable cost and requests
    $vendor_cost_query = "SELECT SUM(`Request_Amount`) AS total_vendor_cost, COUNT(`Vendor_ID`) AS total_vendor_requests FROM `payable_vendors` WHERE `Payable_Status` = 'PENDING'";
    $vendor_cost_result = $conn->query($vendor_cost_query);
    $vendor_summary = $vendor_cost_result->fetch_assoc();

    // Calculate overall totals
    $total_payable = (float)($employee_summary['total_employee_cost'] ?? 0) + (float)($vendor_summary['total_vendor_cost'] ?? 0);
    $total_requests = (int)($employee_summary['total_employee_requests'] ?? 0) + (int)($vendor_summary['total_vendor_requests'] ?? 0);
    $employee_cost = (float)($employee_summary['total_employee_cost'] ?? 0);
    $vendor_cost = (float)($vendor_summary['total_vendor_cost'] ?? 0);

    // Prepare the final response data
    $response['status'] = 'success';
    $response['message'] = 'Payable data fetched successfully.';
    $response['data'] = [
        'summary' => [
            'total_payable' => number_format($total_payable, 2, '.', ''),
            'employee_cost' => number_format($employee_cost, 2, '.', ''),
            'vendor_cost' => number_format($vendor_cost, 2, '.', ''),
            'total_requests' => $total_requests,
            'total_employee_requests' => (int)($employee_summary['total_employee_requests'] ?? 0),
            'total_vendor_requests' => (int)($vendor_summary['total_vendor_requests'] ?? 0)
        ],
        'employee_payables' => $employee_payables,
        'vendor_payables' => $vendor_payables
    ];

} catch (Exception $e) {
    // Handle any general errors
    $response['message'] = 'General Error: ' . $e->getMessage();
} finally {
    // Close the database connection
    if ($conn) {
        $conn->close();
    }
}


// Send the JSON response back to the client
echo json_encode($response);
?>