<?php
// Start output buffering to ensure no output is sent before headers
ob_start();
header('Content-Type: application/json');

try {
    // Include the database connection file.
    // Assuming 'database/connection.php' exists and provides a valid $conn object.
    if (!file_exists('database/connection.php')) {
        throw new Exception('Database connection file not found.');
    }
    include 'database/connection.php';

    // Check if the database connection is valid.
    if (!$conn) {
        throw new Exception('Failed to connect to the database.');
    }

    // Get the requested action from the URL parameters or POST data
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'get_department_budgets':
            // SQL query to retrieve all data from the department_budget table
            // Join with budget_plans and department to get department name and fiscal year
            $sql = "SELECT db.department_budget_id, db.budget_plan_id, db.allocated_amount, db.spent_amount, db.date_time, d.department, bp.fiscal_year 
                    FROM department_budget db
                    JOIN budget_planning bp ON db.budget_plan_id = bp.budget_plan_id
                    JOIN department d ON bp.department_id = d.department_id";
            
            $result = $conn->query($sql);

            $budgets = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Calculate remaining amount
                    $row['remaining_amount'] = $row['allocated_amount'] - $row['spent_amount'];
                    $budgets[] = $row;
                }
                echo json_encode(['success' => true, 'data' => $budgets]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $conn->error]);
            }
            break;

        case 'get_budget_details':
            // Retrieve details for a specific budget based on its ID
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'error' => 'Missing budget ID.']);
                return;
            }

            // SQL query to get budget details, including justification from budget_plans
            $sql = "SELECT db.department_budget_id, db.budget_plan_id, db.allocated_amount, db.spent_amount, db.date_time, d.department, bp.justification, bp.fiscal_year
                    FROM department_budget db
                    JOIN budget_planning bp ON db.budget_plan_id = bp.budget_plan_id
                    JOIN department d ON bp.department_id = d.department_id
                    WHERE db.department_budget_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $budget_detail = $result->fetch_assoc();

            if ($budget_detail) {
                // Calculate remaining amount for the specific budget detail
                $budget_detail['remaining_amount'] = $budget_detail['allocated_amount'] - $budget_detail['spent_amount'];
                echo json_encode(['success' => true, 'data' => $budget_detail]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['success' => false, 'error' => 'Budget not found.']);
            }
            $stmt->close();
            break;
            
        case 'update_spent_amount':
            // Update the spent amount for a specific budget
            $id = $_POST['id'] ?? '';
            $spent_amount = $_POST['spent_amount'] ?? '';

            if (empty($id) || !is_numeric($spent_amount)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid or missing ID or spent amount.']);
                return;
            }

            // The SQL query is updated to use "spent_amount" from the `department_budget` table, which is more specific.
            // It also adds the `date_time` column to be updated to the current timestamp.
            $sql = "UPDATE department_budget SET spent_amount = ?, date_time = NOW() WHERE department_budget_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $spent_amount, $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Spent amount updated successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update spent amount: ' . $stmt->error]);
            }
            $stmt->close();
            break;

        case 'get_dashboard_metrics':
            // SQL query to get total allocated and total spent amounts
            $sql_total_budget = "SELECT SUM(allocated_amount) AS total_allocated, SUM(spent_amount) AS total_spent FROM department_budget";
            $result_total = $conn->query($sql_total_budget);
            $row_total = $result_total->fetch_assoc();
            
            $total_allocated = $row_total['total_allocated'] ?? 0;
            $total_spent = $row_total['total_spent'] ?? 0;
            
            // Calculate utilization percentage and over/under spending
            $utilization_percentage = $total_allocated > 0 ? ($total_spent / $total_allocated) : 0;
            $over_under_spending = $total_spent - $total_allocated;

            // SQL query to count departments with high utilization (e.g., > 90%)
            $sql_alerts = "SELECT COUNT(*) AS alerts_count FROM department_budget WHERE spent_amount / allocated_amount > 0.90";
            $result_alerts = $conn->query($sql_alerts);
            $row_alerts = $result_alerts->fetch_assoc();
            $alerts_count = $row_alerts['alerts_count'] ?? 0;

            echo json_encode([
                'success' => true,
                'data' => [
                    'utilization_percentage' => $utilization_percentage,
                    'over_under_spending' => $over_under_spending,
                    'alerts_count' => $alerts_count
                ]
            ]);
            break;

        case 'generate_report':
            // Start a transaction to ensure all insertions are atomic
            $conn->begin_transaction();
            try {
                $department_budget_id = $_POST['id'] ?? '';
                if (empty($department_budget_id)) {
                    throw new Exception('Missing budget ID.');
                }
                
                // Fetch department ID and the last report date for this budget
                $sql_get_ids_and_last_date = "
                    SELECT bp.department_id, bp.budget_plan_id, MAX(br.date_time) AS last_report_date
                    FROM department_budget db
                    JOIN budget_planning bp ON db.budget_plan_id = bp.budget_plan_id
                    LEFT JOIN budget_report br ON db.department_budget_id = br.department_budget_id
                    WHERE db.department_budget_id = ?
                    GROUP BY bp.department_id, bp.budget_plan_id";
                
                $stmt_fetch = $conn->prepare($sql_get_ids_and_last_date);
                $stmt_fetch->bind_param("i", $department_budget_id);
                $stmt_fetch->execute();
                $result_fetch = $stmt_fetch->get_result();
                $data = $result_fetch->fetch_assoc();
                $stmt_fetch->close();

                if (!$data) {
                    throw new Exception('Budget or department not found.');
                }

                $department_id = $data['department_id'];
                $budget_plan_id = $data['budget_plan_id'];
                $last_report_date = $data['last_report_date'] ?? '1970-01-01 00:00:00'; // Default to a very old date if no previous report exists

                // Generate a unique report ticket number
                $report_ticket = mt_rand(1000000000, 2147483647); // Generates a random number within INT range
                // Check for uniqueness in a loop
                $is_unique = false;
                while (!$is_unique) {
                    $sql_check = "SELECT 1 FROM budget_report WHERE report_ticket = ?";
                    $stmt_check = $conn->prepare($sql_check);
                    $stmt_check->bind_param("i", $report_ticket);
                    $stmt_check->execute();
                    $stmt_check->store_result();
                    if ($stmt_check->num_rows == 0) {
                        $is_unique = true;
                    } else {
                        $report_ticket = mt_rand(1000000000, 2147483647); // Generate a new number if not unique
                    }
                    $stmt_check->close();
                }

                // Insert the main budget report row
                $sql_main_report = "INSERT INTO budget_report (department_budget_id, budget_plan_id, department_id, report_ticket, date_time) VALUES (?, ?, ?, ?, NOW())";
                $stmt_main = $conn->prepare($sql_main_report);
                $stmt_main->bind_param("iiis", $department_budget_id, $budget_plan_id, $department_id, $report_ticket);
                $stmt_main->execute();
                $stmt_main->close();

                // Fetch new payable_employee records since the last report
                $sql_employees = "SELECT Employee_ID FROM payable_employee WHERE department_id = ? AND date_time > ?";
                $stmt_employees = $conn->prepare($sql_employees);
                $stmt_employees->bind_param("is", $department_id, $last_report_date);
                $stmt_employees->execute();
                $result_employees = $stmt_employees->get_result();
                
                // Insert a new budget_report row for each employee payable with the report ticket
                while ($row_employee = $result_employees->fetch_assoc()) {
                    $employee_id = $row_employee['Employee_ID'];
                    $sql_insert_employee = "INSERT INTO budget_report (department_budget_id, employee_id, date_time, department_id, report_ticket) VALUES (?, ?, NOW(), ?, ?)";
                    $stmt_insert_employee = $conn->prepare($sql_insert_employee);
                    $stmt_insert_employee->bind_param("iiis", $department_budget_id, $employee_id, $department_id, $report_ticket);
                    $stmt_insert_employee->execute();
                    $stmt_insert_employee->close();
                }
                $stmt_employees->close();

                // Fetch new payable_vendor records since the last report
                $sql_vendors = "SELECT Vendor_ID FROM payable_vendors WHERE department_id = ? AND date_time > ?";
                $stmt_vendors = $conn->prepare($sql_vendors);
                $stmt_vendors->bind_param("is", $department_id, $last_report_date);
                $stmt_vendors->execute();
                $result_vendors = $stmt_vendors->get_result();

                // Insert a new budget_report row for each vendor payable with the report ticket
                while ($row_vendor = $result_vendors->fetch_assoc()) {
                    $vendor_id = $row_vendor['Vendor_ID'];
                    $sql_insert_vendor = "INSERT INTO budget_report (department_budget_id, vendor_id, date_time, department_id, report_ticket) VALUES (?, ?, NOW(), ?, ?)";
                    $stmt_insert_vendor = $conn->prepare($sql_insert_vendor);
                    $stmt_insert_vendor->bind_param("iiis", $department_budget_id, $vendor_id, $department_id, $report_ticket);
                    $stmt_insert_vendor->execute();
                    $stmt_insert_vendor->close();
                }
                $stmt_vendors->close();
                
                // Commit the transaction if all operations were successful
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Report generated successfully.', 'data' => ['report_ticket' => $report_ticket]]);
                
            } catch (Exception $e) {
                // Rollback the transaction on error
                $conn->rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to generate report: ' . $e->getMessage()]);
            }
            break;

        default:
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'error' => 'Invalid action specified.']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
}

// Close the database connection
if (isset($conn) && $conn) {
    $conn->close();
}
// End output buffering and flush the content
ob_end_flush();
?>