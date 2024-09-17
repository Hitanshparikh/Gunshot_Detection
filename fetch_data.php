<?php
include 'config.php';

header('Content-Type: application/json');

// Fetch the latest data from the database
$sql = "SELECT timestamp, direction, angle FROM gunshot_directions ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No data found']);
}

$conn->close();
?>
