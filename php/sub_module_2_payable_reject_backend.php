<?php
header('Content-Type: application/json');

include 'database/connection.php';

$response = [];

try {
    // Fetch rejected employee tickets and their details by joining tables
    $sqlEmployee = "SELECT
        t.`ticket_Entry`,
        t.`REASON`,
        e.`First_Name`,
        e.`Middle_Name`,
        e.`Last_Name`,
        e.`Job_Tittle` AS position,
        d.`department`,
        e.`Age`,
        e.`Gender`,
        e.`Email`,
        e.`Address`,
        e.`Requested_Amount`,
        e.`due_date`,
        e.`payment_method`,
        e.`justification`
    FROM
        `ticket_payable` AS t
    JOIN
        `payable_employee` AS e ON t.`Employee_ID` = e.`Employee_ID`
    JOIN
        `department` AS d ON e.`department_id` = d.`department_id`
    WHERE
        t.`status` = 'reject' AND t.`Employee_ID` IS NOT NULL";
    $resultEmployee = $conn->query($sqlEmployee);

    $employeeTickets = [];
    $employeePayable = 0;
    if ($resultEmployee->num_rows > 0) {
        while ($row = $resultEmployee->fetch_assoc()) {
            $employeeTickets[] = $row;
            $employeePayable += (float) $row['Requested_Amount'];
        }
    }

    // Fetch rejected vendor tickets and their details by joining tables
    $sqlVendor = "SELECT
        t.`ticket_Entry`,
        t.`REASON`,
        v.`First_Name`,
        v.`Middle_Name`,
        v.`Last_Name`,
        v.`Company_Name`,
        v.`contact_number`,
        v.`Email`,
        v.`Address`,
        v.`Request_Amount`,
        v.`due_date`,
        v.`payment_method`,
        v.`purpose`
    FROM
        `ticket_payable` AS t
    JOIN
        `payable_vendors` AS v ON t.`Vendor_ID` = v.`Vendor_ID`
    WHERE
        t.`status` = 'reject' AND t.`Vendor_ID` IS NOT NULL";
    $resultVendor = $conn->query($sqlVendor);

    $vendorTickets = [];
    $vendorPayable = 0;
    if ($resultVendor->num_rows > 0) {
        while ($row = $resultVendor->fetch_assoc()) {
            $vendorTickets[] = $row;
            $vendorPayable += (float) $row['Request_Amount'];
        }
    }

    $response['success'] = true;
    $response['employee'] = [
        'tickets' => $employeeTickets,
        'totalRejected' => count($employeeTickets),
        'totalPayableRejected' => $employeePayable
    ];
    $response['vendor'] = [
        'tickets' => $vendorTickets,
        'totalRejected' => count($vendorTickets),
        'totalPayableRejected' => $vendorPayable
    ];

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>