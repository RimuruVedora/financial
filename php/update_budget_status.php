<?php
header('Content-Type: application/json');

// Include the database connection file.
require_once '../database/connection.php';

// Check for connection error
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$budget_plan_id = $_POST['budget_plan_id'] ?? null;
$status = $_POST['status'] ?? null;

if ($budget_plan_id === null || $status === null) {
    echo json_encode(['success' => false, 'error' => 'Missing budget_plan_id or status.']);
    exit;
}

$sql = "UPDATE budget_plans SET status = ? WHERE budget_plan_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param("si", $status, $budget_plan_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>