<?php
include 'database/connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

// Add OR_NO and LOCATION to the SELECT statement
$sql = "SELECT collection_id, name, OR_NO, LOCATION, amount, remittance_for, date, time, type FROM collections WHERE status = 'pending'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }
    $response['success'] = true;
}

$conn->close();

echo json_encode($response);
?>