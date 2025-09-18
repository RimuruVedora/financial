<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "financial";
$port = 3306; // ✅ Your custom MySQL port

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

