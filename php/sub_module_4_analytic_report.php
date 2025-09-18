<?php
// php/sub_module_4_analytic_report.php
header('Content-Type: application/json; charset=utf-8');
require 'database/connection.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $totals = ["daily" => 0, "weekly" => 0, "monthly" => 0, "total" => 0];
    $by_department = [];
    $by_category = [];

    $today = new DateTime();
    $start_of_week = (clone $today)->modify('Sunday this week')->setTime(0,0,0);
    $start_of_month = (clone $today)->modify('first day of this month')->setTime(0,0,0);

    // Employees (LEFT JOIN so tickets always count)
    $sql_emp = "
    SELECT tp.date_created, 
           COALESCE(tp.paid_amount, tp.Requested_Amount, pe.Requested_Amount, 0) AS amount,
           pe.payable_type, 
           d.department AS department_name
    FROM ticket_payable tp
    LEFT JOIN payable_employee pe ON tp.Employee_ID = pe.Employee_ID
    LEFT JOIN department d ON pe.department_id = d.department_id
    WHERE tp.status = 'completed'
    ";

    // Vendors (LEFT JOIN so tickets always count)
    $sql_vendor = "
    SELECT tp.date_created, 
           COALESCE(tp.paid_amount, tp.Requested_Amount, pv.Request_Amount, 0) AS amount,
           'Purchase Order' AS payable_type, 
           d.department AS department_name
    FROM ticket_payable tp
    LEFT JOIN payable_vendors pv ON tp.Vendor_ID = pv.Vendor_ID
    LEFT JOIN department d ON pv.department_id = d.department_id
    WHERE tp.status = 'completed'
    ";

    $rows = [];
    foreach ([$sql_emp, $sql_vendor] as $sql) {
        $res = $conn->query($sql);
        if (!$res) {
            throw new Exception("MySQL error: " . $conn->error);
        }
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
    }

    foreach ($rows as $r) {
        $amount = floatval($r['amount']);
        $date = new DateTime($r['date_created']);

        // Totals
        $totals['total'] += $amount;
        if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
            $totals['daily'] += $amount;
        }
        if ($date >= $start_of_week && $date <= $today) {
            $totals['weekly'] += $amount;
        }
        if ($date >= $start_of_month && $date <= $today) {
            $totals['monthly'] += $amount;
        }

        // Department
        $dept = $r['department_name'] ?: 'Unassigned';
        $by_department[$dept] = ($by_department[$dept] ?? 0) + $amount;

        // Category
        $cat = $r['payable_type'] ?: 'Other';
        $by_category[$cat] = ($by_category[$cat] ?? 0) + $amount;
    }

    echo json_encode([
        "success" => true,
        "cards" => array_map(fn($v) => round($v, 2), $totals),
        "department_chart" => [
            "labels" => array_keys($by_department),
            "data" => array_values($by_department),
        ],
        "category_pie" => [
            "labels" => array_keys($by_category),
            "data" => array_values($by_category),
        ]
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "error" => true,
        "message" => $e->getMessage()
    ]);
}
