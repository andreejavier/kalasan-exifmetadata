<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "No image was uploaded or an error occurred."]);
    exit();
}

// Validate and sanitize input data
$user_id = intval($_POST['user_id']);
$latitude = floatval($_POST['lat']);
$longitude = floatval($_POST['lon']);
$date_time = $_POST['date'];
$address = htmlspecialchars($_POST['address'], ENT_QUOTES);
$targetDir = "uploads/";
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

// Handle file upload
$image = $_FILES['image'];
$ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedTypes)) {
    echo json_encode(["success" => false, "message" => "Unsupported file type."]);
    exit();
}

$targetFile = $targetDir . uniqid("plant_", true) . "." . $ext;

if (!move_uploaded_file($image['tmp_name'], $targetFile)) {
    echo json_encode(["success" => false, "message" => "Failed to upload image."]);
    exit();
}

// Save to database
require_once 'db_connection.php'; // Adjust based on your database connection file

$query = "INSERT INTO tree_planted (user_id, latitude, longitude, date_time, address, image_path, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);

if ($stmt->execute([$user_id, $latitude, $longitude, $date_time, $address, $targetFile])) {
    echo json_encode(["success" => true, "message" => "Data saved successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save data."]);
}
?>
