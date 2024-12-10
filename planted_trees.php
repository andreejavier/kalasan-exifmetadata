<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db_connection.php';

// Get the logged-in user's username or ID
$username = $_SESSION['username'];

// Fetch user ID based on username
$user_query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($user_query);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Initialize a variable to hold messages
$message = "";

// Handle deletion of a tree if delete request is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_tree_id'])) {
    $tree_id = $_POST['delete_tree_id'];

    // First, delete related reviews
    $delete_reviews_query = "DELETE FROM reviews WHERE tree_planted_id = ?";
    $stmt = $conn->prepare($delete_reviews_query);
    if ($stmt) {
        $stmt->bind_param('i', $tree_id);
        $stmt->execute();
        $stmt->close();
    }

    // Then, delete the tree record
    $delete_tree_query = "DELETE FROM tree_planted WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_tree_query);
    if ($stmt) {
        $stmt->bind_param('ii', $tree_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $message .= "Tree record and associated reviews deleted successfully. ";
}

// Handle additional image uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['additional_images'])) {
    $tree_id = $_POST['tree_id'];
    $uploads_dir = 'uploads/trees';

    // Ensure upload directory exists
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    foreach ($_FILES['additional_images']['tmp_name'] as $index => $tmp_name) {
        // Ensure the file is not empty
        if (!empty($tmp_name)) {
            $file_type = mime_content_type($tmp_name);

            // Only allow image uploads (JPEG, PNG)
            if (in_array($file_type, ['image/jpeg', 'image/png'])) {
                $image_name = time() . "_" . basename($_FILES['additional_images']['name'][$index]);
                $target_file = "$uploads_dir/$image_name";

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO tree_images (tree_planted_id, image_path) VALUES (?, ?)");
                    $stmt->bind_param('is', $tree_id, $target_file);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    $message .= "Additional images uploaded successfully. ";
}

// Query to fetch tree records with additional images
$query = "SELECT tp.*, ti.image_path AS additional_image
          FROM tree_planted tp
          LEFT JOIN tree_images ti ON tp.id = ti.tree_planted_id
          WHERE tp.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$trees = [];
while ($row = $result->fetch_assoc()) {
    $tree_id = $row['id'];
    if (!isset($trees[$tree_id])) {
        $trees[$tree_id]['details'] = $row;
        $trees[$tree_id]['images'] = [];
    }
    if ($row['additional_image']) {
        $trees[$tree_id]['images'][] = $row['additional_image'];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Kalasan Mapping - Your Planted Trees</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <style>
        .tree-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .tree-card {
            margin-bottom: 20px;
        }

        /* Sidebar base styles with forest theme */
.sidebar {
    width: 260px;
    transition: transform 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    z-index: 1000;
    background-color: #2a513b; /* Forest green */
    color: #e0f5e5; /* Light green text */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Subtle shadow */
}

.sidebar .nav li a {
    font-size: 16px;
    font-weight: 500;
}

.sidebar-wrapper .nav li.active a {
    background-color: #81C784; /* Active background color */
    color: #fff; /* Text color for the active item */
    border-radius: 8px; /* Optional: Rounded corners */
}

.sidebar-wrapper .nav li.active a i {
    color: #fff; /* Icon color for the active item */
}

/* Sidebar logo styles */
.sidebar .logo {
    background-color: #254d36; /* Slightly darker green for logo area */
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #3b6e4d; /* Divider below the logo */
}

.sidebar .logo .simple-text.logo-normal {
    color: #e0f5e5; 
    font-family: 'WoodFont', serif;
    font-size: 22px; 
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Sidebar toggle button */
.menu-toggle {
    font-size: 24px;
    cursor: pointer;
    color: #2a513b;
    margin-right: 15px;
    display: none;
}

/* Main panel adjustment for sidebar */
.main-panel {
    padding-top: 80px;
    transition: margin-left 0.3s ease-in-out;
    margin-left: 260px;
}

.main-panel.expanded {
    margin-left: 0;
}

/* Responsive styles */
@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
    .sidebar {
        transform: translateX(-260px);
    }
    .sidebar.expanded {
        transform: translateX(0);
    }
    .main-panel {
        margin-left: 0;
    }
    .main-panel.expanded {
        margin-left: 260px;
    }

}
    </style>
</head>

<body>
<div class="wrapper">
  <!-- Sidebar -->
  <div class="sidebar" data-color="white" data-active-color="white" id="sidebar">
        <div class="logo">
            <a href="#" class="simple-text logo-mini">
                <div class="logo-image-small">
                    <img src="./assets/img/tree.png" alt="Logo">
                </div>
            </a>
            <a href="#" class="simple-text logo-normal">Kalasan</a>
        </div>
        <div class="sidebar-wrapper">
    <ul class="nav">
        <li>
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
                <i class="nc-icon nc-cloud-upload-94"></i>
                <p>Plant</p>
            </a>
        </li>
        <li class="active">
            <a href="./planted_trees.php">
                <i class="nc-icon nc-chart-bar-32"></i>
                <p>Your Planted</p>
            </a>
        </li>
    </ul>

        </div>
    </div>

    <!-- Main Panel -->
    <div class="main-panel" id="mainPanel">
        <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
            <div class="container-fluid">
                <div class="navbar-wrapper">
                    <span class="menu-toggle" id="menuToggle">
                        <i class="fa fa-bars"></i>
                    </span>
                    <a class="navbar-brand" href="javascript:;">Dashboard & Analytics</a>
                </div>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="nc-icon nc-single-02"></i>
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="profile.php">View Profile</a>
                            <a class="dropdown-item" href="settings.php">Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
          
          <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>

          <div class="row">
            <?php foreach ($trees as $tree): ?>
              <div class="col-md-4 tree-card">
                <div class="card">
                  <img src="<?php echo htmlspecialchars($tree['details']['image_path']); ?>" alt="Main Image" class="card-img-top tree-image">
                  <div class="card-body">
                    <p class="card-text">Location: <?php echo htmlspecialchars($tree['details']['address']); ?></p>
                    <p class="card-text">Date & Time: <?php echo htmlspecialchars($tree['details']['date_time']); ?></p>

                    <?php if (!empty($tree['images'])): ?>
                      <div id="carousel<?php echo $tree['details']['id']; ?>" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                          <?php foreach ($tree['images'] as $index => $image): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                              <img src="<?php echo htmlspecialchars($image); ?>" class="d-block w-100 tree-image" alt="Additional Image">
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <a class="carousel-control-prev" href="#carousel<?php echo $tree['details']['id']; ?>" role="button" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel<?php echo $tree['details']['id']; ?>" role="button" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="sr-only">Next</span>
                        </a>
                      </div>
                    <?php endif; ?>

                    <hr>

<!-- Form for Uploading Additional Images -->
<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="tree_id" value="<?php echo htmlspecialchars($tree['details']['id']); ?>">
    <div class="form-group">
        <label for="additional_images" class="form-label">Upload Additional Images:</label>
        <div class="custom-file">
            <input 
                type="file" 
                name="additional_images[]" 
                id="additional_images" 
                multiple 
                class="custom-file-input"
            >
            <label class="custom-file-label" for="additional_images">Choose files...</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" title="Upload">
        <i class="fa fa-upload"></i>
    </button>
</form>


<!-- Form for Deleting Tree Record -->
<form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this tree record?');">
    <input type="hidden" name="delete_tree_id" value="<?php echo htmlspecialchars($tree['details']['id']); ?>">
    <button type="submit" class="btn btn-danger" title="Delete">
        <i class="fa fa-trash"></i>
    </button>
</form>

                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainPanel = document.getElementById('mainPanel');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('expanded');
        mainPanel.classList.toggle('expanded');
    });
</script>

  <script src="./assets/js/core/jquery.min.js"></script>
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
</body>
</html>
