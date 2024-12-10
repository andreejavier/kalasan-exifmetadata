<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'config/db_connection.php';

$uploads_dir = 'uploads/trees';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

// Fetch all tree records with additional images and uploader details
$query = "SELECT tr.*, ti.image_path AS additional_image, u.username AS uploader_name, u.profile_picture 
          FROM tree_planted tr
          LEFT JOIN tree_images ti ON tr.id = ti.tree_planted_id
          LEFT JOIN users u ON tr.user_id = u.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$trees = [];
while ($row = $result->fetch_assoc()) {
    $trees[$row['id']]['details'] = $row;
    if ($row['additional_image']) {
        $trees[$row['id']]['images'][] = $row['additional_image'];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Kalasan Mapping - All Planted Trees</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .main-content { margin-top: 20px; padding: 20px; }
        .tree-card img { width: 100%; height: 200px; object-fit: cover; }
        .additional-images img { width: 100px; height: 100px; object-fit: cover; margin: 5px; }
        .profile-pic { width: 40px; height: 40px; border-radius: 50%; }
    </style>
</head>
<body>
<div class="content">
    <div class="container-fluid">
        <div class="main-content">
            <h3>All Planted Trees</h3>

            <div class="row">
                <?php foreach ($trees as $tree): ?>
                    <div class="col-md-4 tree-card">
                        <div class="card">
                            <a href="view-tree.php?id=<?php echo htmlspecialchars($tree['details']['id']); ?>">
                                <img src="<?php echo htmlspecialchars($tree['details']['image_path']); ?>" alt="Main Image" class="card-img-top">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">Species Name: 
                                    <?php echo isset($tree['details']['species_name']) ? htmlspecialchars($tree['details']['species_name']) : 'N/A'; ?>
                                </h5>
                                <p>Location: <?php echo htmlspecialchars($tree['details']['address']); ?></p>
                                <p>Date & Time: <?php echo htmlspecialchars($tree['details']['date_time']); ?></p>
                                <p>Uploaded By: 
                                    <img src="<?php echo htmlspecialchars($tree['details']['profile_picture']); ?>" alt="Profile Picture" class="profile-pic"> 
                                    <?php echo htmlspecialchars($tree['details']['uploader_name']); ?>
                                </p>

                                <?php if (isset($tree['images'])): ?>
                                    <div class="additional-images">
                                        <h6>Additional Images:</h6>
                                        <?php foreach ($tree['images'] as $img): ?>
                                            <a href="./view-tree.php?id=<?php echo htmlspecialchars($tree['details']['id']); ?>">
                                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Additional Image">
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
