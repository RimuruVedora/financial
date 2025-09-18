<?php
header('Content-Type: application/json');
require_once 'database/connection.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Failed to connect to database.']);
    exit;
}

try {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'fetch') {
        $sql = "
            SELECT
                br.report_ticket,
                d.department_id,
                d.department AS department_name,
                MAX(br.date_time) AS date_time
            FROM budget_report br
            LEFT JOIN department d ON br.department_id = d.department_id
            GROUP BY br.report_ticket, d.department, d.department_id
        ";
        $result = $conn->query($sql);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => true, 'data' => []]);
        }
    } elseif ($action === 'getDetails') {
        $department_id = isset($_GET['department_id']) ? $_GET['department_id'] : null;

        if ($department_id === null) {
            echo json_encode(['success' => false, 'message' => 'Department ID is required.']);
            exit;
        }

        $details = [
            'department_id' => $department_id,
            'department_name' => '',
            'allocatedAmount' => 0.0,
            'contingencyFund' => 0.0,
            'totalSpent' => 0.0,
            'overageFlag' => false,
            'overagePercentage' => 0.0,
            'overageCause' => 'No overage',
            'overageCausePercentage' => 0.0,
            'totalUpcoming' => 0.0,
            'upcomingPercentage' => 0.0,
            'employees' => [],
            'vendors' => []
        ];

        // Fetch department budget details from `department_budget` table
        $sqlBudget = "
            SELECT
                d.department,
                db.allocated_amount
            FROM department_budget db
            JOIN department d ON db.department_id = d.department_id
            WHERE db.department_id = ?
        ";
        $stmtBudget = $conn->prepare($sqlBudget);
        if (!$stmtBudget) {
            throw new Exception("SQL Prepare Error: " . $conn->error);
        }
        $stmtBudget->bind_param('i', $department_id);
        if (!$stmtBudget->execute()) {
            throw new Exception("SQL Execute Error: " . $stmtBudget->error);
        }
        $resultBudget = $stmtBudget->get_result();
        
        if ($rowBudget = $resultBudget->fetch_assoc()) {
            $details['department_name'] = $rowBudget['department'];
            $details['allocatedAmount'] = floatval($rowBudget['allocated_amount']);
            $details['contingencyFund'] = $details['allocatedAmount'] * 0.10;
        }
        $stmtBudget->close();
        
        // Fetch all employee payables for the department
        $sqlEmployees = "
            SELECT
                pe.Employee_ID,
                pe.First_Name,
                pe.Last_Name,
                pe.Payable_Type,
                pe.Requested_Amount,
                pe.Payable_Status,
                pe.Date_Time,
                pe.justification AS notes
            FROM payable_employee pe
            WHERE pe.department_id = ?
        ";
        $stmtEmployees = $conn->prepare($sqlEmployees);
        if (!$stmtEmployees) {
            throw new Exception("SQL Prepare Error: " . $conn->error);
        }
        $stmtEmployees->bind_param('i', $department_id);
        if (!$stmtEmployees->execute()) {
            throw new Exception("SQL Execute Error: " . $stmtEmployees->error);
        }
        $resultEmployees = $stmtEmployees->get_result();
        
        $payables_by_type = [];
        while ($rowEmployee = $resultEmployees->fetch_assoc()) {
            $rowEmployee['employee_name'] = trim($rowEmployee['First_Name'] . ' ' . $rowEmployee['Last_Name']);
            $details['employees'][] = $rowEmployee;
            $amount = floatval($rowEmployee['Requested_Amount']);
            
            if ($rowEmployee['Payable_Status'] === 'APPROVED') {
                $details['totalSpent'] += $amount;
                $type = $rowEmployee['Payable_Type'];
                if (!isset($payables_by_type[$type])) {
                    $payables_by_type[$type] = 0;
                }
                $payables_by_type[$type] += $amount;
            } elseif ($rowEmployee['Payable_Status'] === 'PENDING') {
                $details['totalUpcoming'] += $amount;
            }
        }
        $stmtEmployees->close();
        
        // Fetch all vendor payables for the department
        $sqlVendors = "
            SELECT
                pv.Vendor_ID,
                pv.Company_Name,
                pv.payable_type AS Payable_Type,
                pv.Request_Amount,
                pv.Payable_Status,
                pv.Date_Time,
                pv.purpose AS notes
            FROM payable_vendors pv
            WHERE pv.department_id = ?
        ";
        $stmtVendors = $conn->prepare($sqlVendors);
        if (!$stmtVendors) {
            throw new Exception("SQL Prepare Error: " . $conn->error);
        }
        $stmtVendors->bind_param('i', $department_id);
        if (!$stmtVendors->execute()) {
            throw new Exception("SQL Execute Error: " . $stmtVendors->error);
        }
        $resultVendors = $stmtVendors->get_result();
        
        while ($rowVendor = $resultVendors->fetch_assoc()) {
            $details['vendors'][] = $rowVendor;
            $amount = floatval($rowVendor['Request_Amount']);
            
            if ($rowVendor['Payable_Status'] === 'APPROVED') {
                $details['totalSpent'] += $amount;
                $type = $rowVendor['Payable_Type'];
                if (!isset($payables_by_type[$type])) {
                    $payables_by_type[$type] = 0;
                }
                $payables_by_type[$type] += $amount;
            } elseif ($rowVendor['Payable_Status'] === 'PENDING') {
                $details['totalUpcoming'] += $amount;
            }
        }
        $stmtVendors->close();
        
        // Determine overage
        if ($details['totalSpent'] > $details['allocatedAmount']) {
            $details['overageFlag'] = true;
            $overage = $details['totalSpent'] - $details['allocatedAmount'];
            if ($details['allocatedAmount'] > 0) {
                $details['overagePercentage'] = round(($overage / $details['allocatedAmount']) * 100, 2);
            }
            
            // Find the overage cause
            if (!empty($payables_by_type)) {
                arsort($payables_by_type);
                $highest_type = key($payables_by_type);
                $highest_amount = $payables_by_type[$highest_type];
                $details['overageCause'] = $highest_type;
                if ($details['totalSpent'] > 0) {
                    $details['overageCausePercentage'] = round(($highest_amount / $details['totalSpent']) * 100, 2);
                }
            }
        }
        
        // Calculate upcoming percentage
        if ($details['allocatedAmount'] > 0) {
            $details['upcomingPercentage'] = round(($details['totalUpcoming'] / $details['allocatedAmount']) * 100, 2);
        }

        echo json_encode(['success' => true, 'data' => $details]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching data. ' . $e->getMessage()]);
}
?>