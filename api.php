<?php
// api.php

header("Content-Type: application/json");
session_start();

// Include the database connection
include 'db_connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $species = isset($_POST['species']) ? $_POST['species'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    
    // Validate the data
    if (empty($species) || empty($location)) {
        echo json_encode(['status' => 'error', 'message' => 'Species and Location are required.']);
        exit();
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO tree_species (species, location, user_id) VALUES (?, ?, ?)");
    $userId = $_SESSION['user_id']; // assuming user_id is stored in session
    $stmt->bind_param("ssi", $species, $location, $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert data.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
?>
