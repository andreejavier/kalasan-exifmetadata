<?php 

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include 'db_connection.php';

// Fetch user details from the database
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT email, profile_picture, id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($email, $profilePicture, $userId);
$stmt->fetch();
$stmt->close();

// Fetch the count of trees planted by this user
$countStmt = $conn->prepare("SELECT COUNT(*) FROM tree_planted WHERE user_id = ?");
$countStmt->bind_param("i", $userId);
$countStmt->execute();
$countStmt->bind_result($treeCount);
$countStmt->fetch();
$countStmt->close();

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="./assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Dashboard - Kalasan</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <link href="./assets/demo/demo.css" rel="stylesheet" />
</head>

<body>
<div class="wrapper">
        <div class="sidebar" data-color="white" data-active-color="danger">
            <div class="logo">
                <a href="./profile.php" class="simple-text logo-mini">
                    <div class="logo-image-small">
                        <img src="assets/img/tree.png" alt="Logo">
                    </div>
                </a>
                <a href="#" class="simple-text logo-normal">Kalasan</a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="active">
                        <a href="./dashboard.php">
                            <i class="nc-icon nc-bank"></i>
                            <p>Home</p>
                        </a>
                    </li>
                    <li>
                        <a href="./map.php">
                            <i class="nc-icon nc-pin-3"></i>
                            <p>Maps</p>
                        </a>
                    </li>
                    <li>
                        <a href="./upload-plant.php">
                            <i class="nc-cloud-upload-94"></i>
                            <p>Plant</p>
                        </a>
                    </li>
                    <li>
                        <a href="./planted_trees.php">
                            <i class="nc-icon nc-chart-bar-32"></i>
                            <p>Your Planted</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel" style="height: 100vh;">
            <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:;">Profile</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="description">Profile</h3>

                        <!-- Display profile picture and user details -->
                        <div class="profile-section">
                            <?php if (!empty($profilePicture)): ?>
                                <img src="uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-picture" style="width:150px; height:150px; object-fit:cover;">
                            <?php else: ?>
                                <img src="default-profile.png" alt="Default Profile Picture" class="profile-picture" style="width:150px; height:150px; object-fit:cover;">
                            <?php endif; ?>

                            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                            <p><strong>Observations:</strong> <?php echo htmlspecialchars($treeCount); ?></p>
                            
                            <!-- Add Edit Profile button -->
                            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <nav class="footer-nav">
                            <ul>
                                <li><a href="#" target="_blank">Kalasan Team</a></li>
                                <li><a href="#" target="_blank">Blog</a></li>
                                <li><a href="#" target="_blank">Licenses</a></li>
                            </ul>
                        </nav>
                        <div class="credits ml-auto">
                            <span class="copyright">
                                © 2024, made with <i class="fa fa-heart heart"></i> by Kalasan Team
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="./assets/js/core/jquery.min.js"></script>
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>
    <script src="./assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <script src="./assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>
</body>

</html>
