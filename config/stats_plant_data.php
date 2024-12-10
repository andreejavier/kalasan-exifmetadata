<?php 
session_start(); // Start the session

// Check if user is logged in and the session variable 'user_id' is set
if (!isset($_SESSION['user_id'])) {
    // Return an error message as JSON if user_id is not found
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in.']);
    exit; // Stop further execution
}

// Include database connection
include 'db_connection.php';

// Check if the database connection is established
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Fetch the logged-in user's user_id from the session
$user_id = $_SESSION['user_id'];

try {
    // SQL query to fetch plant data for the logged-in user
    $sql = "SELECT * FROM newtree_species WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    // Bind the user_id to the query
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    // Bind the result columns to variables
    $result = $stmt->get_result();
    
    $plants = array();
    while ($row = $result->fetch_assoc()) {
        $plants[] = $row;
    }

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($plants);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Return error message as JSON in case of failure
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>
