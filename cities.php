<?php
require_once 'config.php';

// Database connection
$conn = getMysqliConnection();

$sql = "SELECT DISTINCT city FROM hotels"; // Select distinct city names from hotels table
$result = $conn->query($sql);

$cities = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cities[] = $row['city'];
    }
    echo json_encode($cities);
} else {
    echo json_encode([]);
}

$conn->close();
?>
