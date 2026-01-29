<?php
require_once 'config.php';

// Database connection
$conn = getMysqliConnection();

$sql = "SELECT id, name, city, district FROM hotels";
$result = $conn->query($sql);

$hotels = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    echo json_encode($hotels);
} else {
    echo json_encode([]);
}

$conn->close();
?>