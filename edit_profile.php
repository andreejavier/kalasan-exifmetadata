<?php 
// edit_profile.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include 'db_connection.php';

$username = $_SESSION['username'];

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = $_POST['email'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['profile_picture']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Move the file to the uploads directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFilePath)) {
            // Update the database with new email and profile picture
            $stmt = $conn->prepare("UPDATE users SET email = ?, profile_picture = ? WHERE username = ?");
            $stmt->bind_param("sss", $newEmail, $fileName, $username);
        } else {
            echo "Failed to upload the file.";
        }
    } else {
        // Update only the email if no new picture is uploaded
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE username = ?");
        $stmt->bind_param("ss", $newEmail, $username);
    }

    // Execute and close the statement
    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

// Fetch current user details
$stmt = $conn->prepare("SELECT email, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($email, $profilePicture);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Edit Profile - Kalasan</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <?php if (!empty($profilePicture)): ?>
                    <img src="uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-picture" style="width:100px; height:100px; object-fit:cover;">
                <?php else: ?>
                    <img src="default-profile.png" alt="Default Profile Picture" class="profile-picture" style="width:100px; height:100px; object-fit:cover;">
                <?php endif; ?>
                <input type="file" name="profile_picture" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
