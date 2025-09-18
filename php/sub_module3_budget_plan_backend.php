<?php
// Start output buffering to prevent unexpected output before the JSON response
ob_start();
header('Content-Type: application/json');

try {
    // Check if the connection file exists before including it
    if (!file_exists('database/connection.php')) {
        throw new Exception('Connection file not found!');
    }
    
    // The include statement is now within the try block to catch potential errors
    include 'database/connection.php';

    // Check if the connection object is valid
    if (!$conn) {
        // This is now explicitly handled as a server error
        throw new Exception('Database connection failed.');
    }

    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_budget_plans':
        case 'get_pending_budget_plans':
            // Function to retrieve budget plans with department name
            function getBudgetPlans($conn, $status = null) {
                $sql = "SELECT bp.budget_plan_id, d.department, bp.submitted_by, bp.proposed_amount, bp.fiscal_year, bp.justification, bp.attached_file, bp.status, bp.rejection_reason FROM budget_planning bp JOIN department d ON bp.department_id = d.department_id";
                if ($status !== null) {
                    $sql .= " WHERE bp.status = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $status);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $conn->query($sql);
                }
                
                $plans = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $plans[] = $row;
                    }
                    echo json_encode($plans);
                } else {
                    // Send a 500 status code for database query errors
                    http_response_code(500); 
                    echo json_encode(['error' => 'Database query failed: ' . ($stmt->error ?? $conn->error)]);
                }
                if (isset($stmt)) $stmt->close();
            }
            
            if ($action === 'get_pending_budget_plans') {
                getBudgetPlans($conn, 'pending');
            } else {
                getBudgetPlans($conn);
            }
            break;

        case 'update_status':
            // Function to update the status of a budget plan
            function updateBudgetPlanStatus($conn) {
                // Get the budget plan ID and new status from the POST request
                $id = $_POST['id'] ?? '';
                $status = $_POST['status'] ?? '';
                $rejection_reason = $_POST['rejection_reason'] ?? NULL; // Capture the rejection reason

                if (empty($id) || empty($status)) {
                    // Send a 400 status code for bad requests
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing ID or status parameter.']);
                    return;
                }
                
                // Prepare the SQL statement to prevent SQL injection
                if ($status === 'rejected') {
                    $stmt = $conn->prepare("UPDATE budget_planning SET status = ?, rejection_reason = ? WHERE budget_plan_id = ?");
                    $stmt->bind_param("ssi", $status, $rejection_reason, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE budget_planning SET status = ?, rejection_reason = NULL WHERE budget_plan_id = ?");
                    $stmt->bind_param("si", $status, $id);
                }

                if ($stmt->execute()) {
                    if ($status === 'approved') {
                        // If approved, insert into department_budget table
                        insertApprovedBudget($conn, $id);
                    }
                    echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
                } else {
                    // Send a 500 status code for execution errors
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to update status: ' . $stmt->error]);
                }
                $stmt->close();
            }

            // New function to insert approved budget into department_budget table
            function insertApprovedBudget($conn, $budget_plan_id) {
                // First, get the proposed amount from the budget_planning table
                $stmt_get = $conn->prepare("SELECT proposed_amount, department_id FROM budget_planning WHERE budget_plan_id = ?");
                $stmt_get->bind_param("i", $budget_plan_id);
                $stmt_get->execute();
                $result = $stmt_get->get_result();
                $row = $result->fetch_assoc();
                $proposed_amount = $row['proposed_amount'];
                $department_id = $row['department_id']; // Get the department ID
                $stmt_get->close();

                if ($proposed_amount !== null) {
                    // Check if a record already exists for this budget_plan_id to prevent duplicates
                    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM department_budget WHERE budget_plan_id = ?");
                    $stmt_check->bind_param("i", $budget_plan_id);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();
                    $row_check = $result_check->fetch_row();
                    $count = $row_check[0];
                    $stmt_check->close();
                    
                    if ($count == 0) {
                        // Insert the new record with the proposed amount and current timestamp
                        $stmt_insert = $conn->prepare("INSERT INTO department_budget (budget_plan_id, department_id, allocated_amount, spent_amount, date_time) VALUES (?, ?, ?, 0, NOW())");
                        $stmt_insert->bind_param("iid", $budget_plan_id, $department_id, $proposed_amount);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    } else {
                        // You can handle this case, e.g., update the existing record
                        $stmt_update = $conn->prepare("UPDATE department_budget SET allocated_amount = ?, department_id = ? WHERE budget_plan_id = ?");
                        $stmt_update->bind_param("dii", $proposed_amount, $department_id, $budget_plan_id);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                }
            }

            updateBudgetPlanStatus($conn);
            break;
            
        default:
            // Send a 400 status code for invalid actions
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action parameter.']);
            break;
    }

} catch (Exception $e) {
    // Catch any exceptions and return a JSON formatted error message
    http_response_code(500); // Set a 500 status code for server errors
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
}

// Close the database connection if it exists
if (isset($conn) && $conn) {
    $conn->close();
}

// End output buffering and send the content to the browser
ob_end_flush();
?>