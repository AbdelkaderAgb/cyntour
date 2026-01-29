<?php
require_once '../config.php';

// Database connection
$conn = getMysqliConnection();

// Query to fetch hotel names
$sql = "SELECT DISTINCT hotel_name FROM pricing_data";
$result = $conn->query($sql);

// Build the response array
$response = array('hotels' => array());

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response['hotels'][] = $row['hotel_name'];
    }
} else {
    $response['message'] = 'No hotels found.';
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
