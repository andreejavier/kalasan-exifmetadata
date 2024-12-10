<?php
// PDO Database Connection
$host = 'localhost';
$dbname = 'proj-kalasan_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit();
}

// Check if the form data is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['lat'] ?? null;
    $longitude = $_POST['lon'] ?? null;
    $date = $_POST['date'] ?? null; // Date is optional
    $address = $_POST['address'] ?? null;
    $userId = $_POST['user_id'] ?? null;

    // Validate required fields
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        exit();
    }
    if (!$latitude || !$longitude || !$address) { // Exclude date from mandatory check
        echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
        exit();
    }

    // Handle image upload
    $imageFilePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $uploadDir = __DIR__ . '/uploads/'; // Absolute path to the uploads directory

        // Create the uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                echo json_encode(['success' => false, 'message' => 'Failed to create uploads directory']);
                exit();
            }
        }

        // Generate a unique file name for the image
        $imageFilePath = 'uploads/' . uniqid() . '-' . basename($imageName);
        $absoluteFilePath = $uploadDir . basename($imageFilePath);

        // Move uploaded file to the uploads directory
        if (!move_uploaded_file($imageTmpPath, $absoluteFilePath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save image']);
            exit();
        }
    }

    // Insert data into the database
    try {
        if ($date) {
            // Include date_time if provided
            $stmt = $pdo->prepare("INSERT INTO tree_planted (user_id, latitude, longitude, date_time, address, image_path) 
                                   VALUES (:user_id, :lat, :lon, :date, :address, :image_path)");
            $stmt->bindParam(':date', $date);
        } else {
            // Exclude date_time if not provided
            $stmt = $pdo->prepare("INSERT INTO tree_planted (user_id, latitude, longitude, address, image_path) 
                                   VALUES (:user_id, :lat, :lon, :address, :image_path)");
        }

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':lat', $latitude);
        $stmt->bindParam(':lon', $longitude);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':image_path', $imageFilePath);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save data']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving data: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
