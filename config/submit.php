<?php
// Database connection
include 'db_connection.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the tree_species_id from the URL (edit.php?tree_species_id=1)
$tree_species_id = isset($_GET['tree_species_id']) ? (int)$_GET['tree_species_id'] : 0;

// Fetch tree species data from database
$sql = "SELECT * FROM tree_species WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tree_species_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the row data
    $tree_species = $result->fetch_assoc();
} else {
    die("Record not found.");
}

// Close connection
$stmt->close();
$conn->close();
?>
