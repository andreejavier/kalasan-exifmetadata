<?php
// Database connection
include 'db_connection.php';

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tree species data
$speciesDataQuery = "SELECT address, COUNT(*) AS count FROM tree_planted GROUP BY address";
$speciesDataResult = $conn->query($speciesDataQuery);
$speciesData = [];
while ($row = $speciesDataResult->fetch_assoc()) {
    $speciesData[] = $row;
}

// Fetch uploads over time data
$uploadsDataQuery = "SELECT DATE(created_at) AS created_at, COUNT(*) AS count FROM tree_planted GROUP BY created_at";
$uploadsDataResult = $conn->query($uploadsDataQuery);
$uploadsData = [];
while ($row = $uploadsDataResult->fetch_assoc()) {
    $uploadsData[] = $row;
}

// Return JSON response
echo json_encode([
    'speciesData' => $speciesData,
    'uploadsData' => $uploadsData
]);

// Close connection
$conn->close();
?>
