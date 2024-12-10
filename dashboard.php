<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dev_kalasan_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for dashboard stats (Only for the logged-in user)
$sql = "SELECT COUNT(*) AS contributor_count FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$contributor_count = ($result->num_rows > 0) ? $result->fetch_assoc()['contributor_count'] : 0;

$sql = "SELECT COUNT(*) AS planted_tree FROM tree_planted WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$planted_tree = ($result->num_rows > 0) ? $result->fetch_assoc()['planted_tree'] : 0;

// Fetch data for the chart (trees by category for the logged-in user)
$sql = "SELECT category, COUNT(*) AS count FROM tree_planted WHERE user_id = ? GROUP BY category";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$chartData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chartData[] = $row;
    }
}

// Fetch data for uploaded trees over time (Only for the logged-in user)
$sql = "SELECT DATE(created_at) AS date_time, COUNT(*) AS count FROM tree_planted WHERE user_id = ? GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$uploadedData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $uploadedData[] = $row;
    }
}

// Fetch data for the species breakdown chart (Only for the logged-in user)
$sql = "SELECT species_name, COUNT(*) AS count FROM tree_planted WHERE user_id = ? GROUP BY species_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$speciesData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $speciesData[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalasan Dashboard & Analytics</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/paper-dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .wrapper { display: flex; height: 100vh; }
        .main-panel {padding-top: 80px; transition: margin-left 0.3s; }
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
                <i class="nc-icon nc-cloud-upload-94"></i>
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

        <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <p class="card-category">Followers</p>
                            <p class="card-title"><?php echo $contributor_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <p class="card-category">Your Observations</p>
                            <p class="card-title"><?php echo $planted_tree; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
    <!-- Tree Analytics by Category -->
    <div class="col-lg-6 col-md-12">
        <div class="card card-stats">
            <div class="card-body">
                <h3>Tree Analytics by Category</h3>
                <div style="max-width: 300px;  height: 400px; margin: 0 auto;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded Over Time Chart (Right side of the Tree Analytics chart) -->
    <div class="col-lg-6 col-md-12">
        <div class="card card-stats">
            <div class="card-body">
                <h3>Uploaded Over Time</h3>
                <div style="max-width: 500px; height: 400px; margin: 0 auto;">
                    <canvas id="uploadedOverTimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


            <h3>Tree Analytics by Species</h3>
            <canvas id="speciesChart"></canvas>
        </div>
    </div>
</div>

<script>
    const forestPalette = ['#2E7D32', '#4CAF50', '#81C784', '#388E3C', '#1B5E20', '#76FF03', '#43A047', '#689F38', '#33691E', '#8BC34A'];

    const chartData = <?php echo json_encode($chartData); ?>;
    const categories = chartData.map(data => data.category);
    const categoryCounts = chartData.map(data => data.count);
    const categoryColors = categories.map((_, i) => forestPalette[i % forestPalette.length]);

    const speciesData = <?php echo json_encode($speciesData); ?>;
    const speciesNames = speciesData.map(data => data.species_name);
    const speciesCounts = speciesData.map(data => data.count);
    const speciesColors = speciesNames.map((_, i) => forestPalette[i % forestPalette.length]);

    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [{
                data: categoryCounts,
                backgroundColor: categoryColors
            }]
        }
    });

    new Chart(document.getElementById('speciesChart'), {
        type: 'bar',
        data: {
            labels: speciesNames,
            datasets: [{
                label: 'Count',
                data: speciesCounts,
                backgroundColor: speciesColors
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    // Example: Fetch data for uploaded trees over time
const uploadedData = <?php echo json_encode($uploadedData); ?>; // You should replace this with actual data from the database

const uploadDates = uploadedData.map(data => data.date_time); // Extracting dates
const uploadedCounts = uploadedData.map(data => data.count); // Extracting counts

// Initialize the uploaded over time chart
const ctxUploadedOverTime = document.getElementById('uploadedOverTimeChart').getContext('2d');
new Chart(ctxUploadedOverTime, {
    type: 'line', // Line chart for showing uploads over time
    data: {
        labels: uploadDates,
        datasets: [{
            label: 'Trees Uploaded Over Time',
            data: uploadedCounts,
            borderColor: '#42A5F5', // Line color
            backgroundColor: 'rgba(66, 165, 245, 0.2)', // Light blue background color for the line
            borderWidth: 2,
            tension: 0.4 // Smooth line
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        },
        responsive: true,
        maintainAspectRatio: false,
    }
});

</script>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainPanel = document.getElementById('mainPanel');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('expanded');
        mainPanel.classList.toggle('expanded');
    });
</script>

<script src="assets/js/core/jquery.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>
</body>
</html>
