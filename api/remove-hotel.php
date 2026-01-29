<?php
include 'auth.php'; // Include auth.php to restrict access
require_once '../config.php';

// Database connection
$conn = getMysqliConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hotel_name'])) {
    $hotelName = $conn->real_escape_string($_POST['hotel_name']);
    
    $deleteQuery = "DELETE FROM pricing_data WHERE hotel_name = '$hotelName'";
    
    if ($conn->query($deleteQuery) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
}

$conn->close();
?>
