<?php
session_start();
include 'config/db_connection.php'; // Ensure this points to your DB connection file

// Check if id is set
if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$tree_id = $_GET['id'];

// Query to fetch plant data and the uploader's info (username and number of plants uploaded)
$sql = "SELECT t.*, u.username, u.profile_picture, COUNT(t2.id) AS observations_count
        FROM `tree_planted` t
        JOIN `users` u ON t.user_id = u.id
        LEFT JOIN `tree_planted` t2 ON t2.user_id = u.id
        WHERE t.id = ? 
        GROUP BY u.id";

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Query preparation failed: ' . $conn->error);
}

// Bind the parameter (tree_id) to the query
$stmt->bind_param("i", $tree_id);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // No record found
    echo "No tree found.";
    exit();
}

// Fetch the tree record and uploader info
$tree = $result->fetch_assoc();

// Query to fetch other plants by the same user
$other_plants_sql = "SELECT t.id, t.species_name, t.image_path 
                     FROM `tree_planted` t
                     WHERE t.user_id = ? AND t.id != ?"; // Excluding current plant
$stmt = $conn->prepare($other_plants_sql);
$stmt->bind_param("ii", $tree['user_id'], $tree_id);
$stmt->execute();
$other_plants_result = $stmt->get_result();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Tree Details</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <!-- CSS for Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        #map { height: 250px; width: 100%; margin-top: 20px; border: 1px solid #ddd; }
        .tree-image { width: 100%; height: auto; }
        .species-details { font-style: italic; color: #555; }
        .user-info { display: flex; align-items: center; gap: 10px; margin-top: 20px; }
        .user-info img { border-radius: 50%; width: 40px; height: 40px; }
        .map-details { text-align: center; margin-top: 10px; font-size: 0.9em; color: #777; }

        /* Flexbox layout for main content and footer */
        .main-panel {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
        }

        footer.footer {
            background: #f4f4f4;
            padding: 15px;
            text-align: center;
        }
        .other-plants {
            margin-top: 40px;
        }
        .other-plants .plant-card {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .other-plants .plant-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .other-plants .plant-card .plant-info {
            flex: 1;
        }
    </style>
</head>
<body class="">
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-color="white" data-active-color="danger">
      <div class="logo">
        <a href="#" class="simple-text logo-mini">
          <img src="assets/img/pngtree-banyan-tree-logo-design-vector-png-image_6131481.png" alt="Logo">
        </a>
        <a href="#" class="simple-text logo-normal">Kalasan</a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="active">
            <a href="./home.php">
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
            <a href="./manage-record.php">
              <i class="nc-icon nc-cloud-upload-94"></i>
              <p>Manage Records</p>
            </a>
          </li>
          <li>
            <a href="./tree-species-form.php">
              <i class="nc-icon nc-cloud-upload-94"></i>
              <p>tree Species</p>
            </a>
          </li>
          <li>
            <a href="./contributors-datatable.php">
              <i class="nc-icon nc-tile-56"></i>
              <p>Manage User</p>
            </a>
          </li>
          <li>
            <a href="./stats.php">
              <i class="nc-icon nc-tile-56"></i>
              <p>Manage User</p>
            </a>
            <li>
            <a href="./validate-records.php">
              <i class="nc-icon nc-tile-56"></i>
              <p>Valadate Records</p>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand" href="javascript:;">Tree Details</a>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->

      <!-- Main Content -->
      <div class="content">
        <div class="container">
          <div class="header-section">
            <div>
              <h2><?php echo htmlspecialchars($tree['species_name']); ?></h2>
              <p class="species-details">(<?php echo htmlspecialchars($tree['scientific_name']); ?>)</p>
            </div>
          </div>

          <!-- Bootstrap Card -->
          <div class="card mb-4">
            <div class="card-body">
              <!-- Image and Map in one card -->
              <div class="row">
                <div class="col-md-6">
                  <!-- Displaying image -->
                  <?php if (!empty($tree['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($tree['image_path']); ?>" alt="Tree Image" class="tree-image">
                  <?php else: ?>
                    <p>No image available.</p>
                  <?php endif; ?>
                </div>
                <div class="col-md-6">

                 <!-- Additional Information -->
              <div class="user-info mt-4">
                <img src="<?php echo !empty($tree['profile_picture']) ? htmlspecialchars($tree['profile_picture']) : 'assets/img/user-avatar.png'; ?>" alt="User Avatar">
                <div>
                  <p><strong>Uploaded by:</strong> <a href="#"><?php echo htmlspecialchars($tree['username']); ?></a></p>
                    </div>
              </div>
                  <!-- Map to display plant location -->
                  <div id="map"></div>
                  <div class="map-details">
                    <p><strong>Location Map:</strong> <?php echo htmlspecialchars($tree['address']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($tree['description']); ?></p>
                  </div>
                </div>
              </div>

            

              <div class="observation-details mt-3">
                <p><strong>Location:</strong> <?php echo htmlspecialchars($tree['address']); ?></p>
                <p><strong>Date Observed:</strong> <?php echo htmlspecialchars($tree['date_time']); ?></p>
                <p><strong>Submitted:</strong> <?php echo htmlspecialchars($tree['created_at']); ?></p>
              </div>
            </div>
          </div>
          <!-- End of Card -->

          <!-- Other plants section -->
          <div class="other-plants">
            <h3>Other Plants by <?php echo htmlspecialchars($tree['username']); ?></h3>
            <?php while ($other_plant = $other_plants_result->fetch_assoc()): ?>
              <div class="plant-card">
                <img src="<?php echo htmlspecialchars($other_plant['image_path']); ?>" alt="Other Plant">
                <div class="plant-info">
                  <h5><?php echo htmlspecialchars($other_plant['species_name']); ?></h5>
                  <a href="view-tree.php?id=<?php echo $other_plant['id']; ?>">View Details</a>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <div class="container-fluid">
          <div class="row">
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

  <!-- Leaflet Map JS -->
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  <script>
      const latitude = <?php echo !empty($tree['latitude']) ? $tree['latitude'] : 'null'; ?>;
      const longitude = <?php echo !empty($tree['longitude']) ? $tree['longitude'] : 'null'; ?>;
      if (latitude && longitude) {
        const map = L.map('map').setView([latitude, longitude], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map)
          .bindPopup("<?php echo htmlspecialchars($tree['species_name']); ?>")
          .openPopup();
      }
  </script>
</body>
</html>
