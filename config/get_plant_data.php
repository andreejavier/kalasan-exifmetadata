<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proj-kalasan_db";

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for a connection error
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// SQL query to retrieve plant data
$sql = "SELECT * FROM tree_records";
$result = $conn->query($sql);

// Initialize an empty array to store plant data
$plants = [];

// Check if the query returned results
if ($result && $result->num_rows > 0) {
    // Loop through the result set and add each row to the $plants array
    while ($row = $result->fetch_assoc()) {
        $plants[] = [
            "id" => (int)$row['id'],
            "image" => htmlspecialchars($row['image']),
            "latitude" => (float)$row['latitude'],
            "longitude" => (float)$row['longitude'],
            "date_time" => htmlspecialchars($row['date_time']),
            "address" => htmlspecialchars($row['address']),
            "user_id" => (int)$row['user_id'],
        ];
    }
    // Send the plant data as JSON
    echo json_encode($plants, JSON_PRETTY_PRINT);
} else {
    // No records found
    http_response_code(404); // Not Found
    echo json_encode(["message" => "No plant data found"]);
}

// Close the database connection
$conn->close();
?>
