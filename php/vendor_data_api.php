<?php
// Include your existing database connection file
require_once __DIR__ . '/database/connection.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Function to fetch data from the database
function getVendorData($conn) {
    $sql = "SELECT pav.*, v.Company_Name, v.First_Name, v.Last_Name, v.contact_number, v.Email, v.Address
            FROM payable_accounts_vendor pav
            JOIN vendors v ON pav.Vendor_ID = v.Vendor_ID";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

$vendor_data = getVendorData($conn);

// Close the database connection
$conn->close();

// Return data as JSON
echo json_encode($vendor_data);
?>