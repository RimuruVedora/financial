<?php
include 'database/connection.php';

header('Content-Type: application/json');
$response = ['employees' => [], 'vendors' => [], 'error' => ''];

// Handle DB errors strictly
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ Handle update request from modal (when clicking "Yes")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $ticketEntry = $data['ticket_Entry'] ?? null;
    $requestedAmount = $data['Requested_Amount'] ?? null;
    $paidAmount = $data['paid_amount'] ?? null;

    if ($ticketEntry) {
        try {
            $conn->begin_transaction();

            // 1️⃣ Update ticket_payable (reuse date_created as completion date)
            $stmt = $conn->prepare("
                UPDATE ticket_payable
                SET Requested_Amount = ?, 
                    paid_amount = ?, 
                    balance = (? - ?), 
                    status = 'completed',
                    date_created = NOW()  -- ✅ overwrite with current datetime
                WHERE ticket_Entry = ? AND status = 'accept'
            ");
            $stmt->bind_param("iiiss", $requestedAmount, $paidAmount, $requestedAmount, $paidAmount, $ticketEntry);
            $stmt->execute();

            // 2️⃣ Get department_id from employee or vendor
            $sqlDept = "
                SELECT pe.department_id
                FROM ticket_payable tp
                INNER JOIN payable_employee pe ON tp.Employee_ID = pe.Employee_ID
                WHERE tp.ticket_Entry = ?
                UNION
                SELECT pv.department_id
                FROM ticket_payable tp
                INNER JOIN payable_vendors pv ON tp.Vendor_ID = pv.Vendor_ID
                WHERE tp.ticket_Entry = ?
            ";
            $stmtDept = $conn->prepare($sqlDept);
            $stmtDept->bind_param("ss", $ticketEntry, $ticketEntry);
            $stmtDept->execute();
            $resultDept = $stmtDept->get_result();

            if ($row = $resultDept->fetch_assoc()) {
                $departmentId = $row['department_id'];

                if ($departmentId) {
                    // 3️⃣ Update department budget (add to spent_amount)
                    $stmtBudget = $conn->prepare("
                        UPDATE department_budget 
                        SET spent_amount = spent_amount + ? 
                        WHERE department_id = ?
                    ");
                    $stmtBudget->bind_param("ii", $paidAmount, $departmentId);
                    $stmtBudget->execute();
                }
            }

            // 4️⃣ Update revenue (reduce by paid amount)
            $stmtRevenue = $conn->prepare("
                UPDATE revenue 
                SET Revenue = Revenue - ? 
                WHERE Revenue_ID = 1
            ");
            $stmtRevenue->bind_param("i", $paidAmount);
            $stmtRevenue->execute();

            $conn->commit();

            echo json_encode(["success" => true, "message" => "Disbursement updated, department budget adjusted, revenue decreased, and date updated."]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        $conn->close();
        exit; // ✅ stop here so it won’t continue to fetch section
    }
}

try {
    // ✅ Employee disbursements
    $sqlEmployees = "
        SELECT 
            tp.payable_ID,
            tp.ticket_Entry,
            pe.First_Name,
            pe.Middle_Name,
            pe.Last_Name,
            pe.Requested_Amount,
            pe.payment_method,
            pe.due_date,
            pe.payable_type,
            tp.status,
            pe.contact AS contact_number
        FROM ticket_payable tp
        INNER JOIN payable_employee pe 
            ON tp.Employee_ID = pe.Employee_ID
        WHERE tp.status = 'accept'
    ";

    $resultEmp = $conn->query($sqlEmployees);
    while ($row = $resultEmp->fetch_assoc()) {
        $response['employees'][] = [
            'full_name'       => trim($row['First_Name'].' '.$row['Middle_Name'].' '.$row['Last_Name']),
            'ticket_Entry'    => $row['ticket_Entry'],
            'Requested_Amount'=> $row['Requested_Amount'],
            'payment_method'  => $row['payment_method'],
            'due_date'        => $row['due_date'],
            'payable_type'    => $row['payable_type'],
            'status'          => $row['status'],
            'contact_number'  => $row['contact_number']
        ];
    }

    // ✅ Vendor disbursements
    $sqlVendors = "
        SELECT 
            tp.payable_ID,
            tp.ticket_Entry,
            pv.Company_Name,
            pv.Request_Amount,
            pv.payment_method,
            pv.due_date,
            tp.status,
            pv.contact_number
        FROM ticket_payable tp
        INNER JOIN payable_vendors pv 
            ON tp.Vendor_ID = pv.Vendor_ID
        WHERE tp.status = 'accept'
    ";

    $resultVen = $conn->query($sqlVendors);
    while ($row = $resultVen->fetch_assoc()) {
        $response['vendors'][] = [
            'name'            => $row['Company_Name'],
            'ticket_Entry'    => $row['ticket_Entry'],
            'Requested_Amount'=> $row['Request_Amount'],
            'payment_method'  => $row['payment_method'],
            'due_date'        => $row['due_date'],
            'status'          => $row['status'],
            'contact_number'  => $row['contact_number']
        ];
    }

    // ✅ Pending ticket count
    $sqlPending = "SELECT COUNT(*) AS pending_count FROM ticket_payable WHERE status = 'accept'";
    $resultPending = $conn->query($sqlPending);
    $response['pending_count'] = $resultPending->fetch_assoc()['pending_count'] ?? 0;

    // ✅ Completed transactions count
    $sqlCompleted = "SELECT COUNT(*) AS completed_count FROM ticket_payable WHERE status = 'completed'";
    $resultCompleted = $conn->query($sqlCompleted);
    $response['completed_count'] = $resultCompleted->fetch_assoc()['completed_count'] ?? 0;

    // ✅ Total disbursement (sum of paid amounts)
    $sqlTotal = "SELECT COALESCE(SUM(paid_amount), 0) AS total_disbursement FROM ticket_payable WHERE status = 'completed'";
    $resultTotal = $conn->query($sqlTotal);
    $response['total_disbursement'] = $resultTotal->fetch_assoc()['total_disbursement'] ?? 0;

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>
