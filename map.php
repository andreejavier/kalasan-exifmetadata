<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include 'db_connection.php';

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <!-- External CSS files -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <link href="assets/css/custom-dashboard.css" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet and MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.Default.css" />

    <link href="assets/demo/demo.css" rel="stylesheet" />

    <style>
        #map {
            height: 500px;
            width: 100%;
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
        <li class="active">
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
                    <a class="navbar-brand" href="javascript:;">Map</a>
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

      <!-- Map Content -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <div id="map"></div> <!-- Map container -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-6">
              <nav class="footer-nav">
                <ul>
                  <li><a href="#">About Us</a></li>
                  <li><a href="#">Blog</a></li>
                  <li><a href="#">Licenses</a></li>
                </ul>
              </nav>
            </div>
            <div class="col-md-6 text-right">
              <p>&copy; <script>document.write(new Date().getFullYear())</script> made with <i class="fa fa-heart heart"></i> by kalasan Team.</p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>

  <!-- JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.3.0/dist/leaflet.markercluster.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
  // Step 1: Initialize the map
  var map = L.map('map').setView([8.358062751051879, 124.86094951519246], 11); // Initial view

  // Step 2: Add the tile layers (map backgrounds)
  var satelliteLayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors, Tiles by OSM France'
  });

  var osmLayer = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_satellite/{z}/{x}/{y}{r}.jpg', {
    attribution: '© OpenStreetMap contributors'
  });

  // Add OpenStreetMap layer by default
  osmLayer.addTo(map);

  // Step 3: Add a layer control to toggle between OSM and Satellite views
  var baseLayers = {
    "Street Map": satelliteLayer,
    "Satellite View": osmLayer,
  };

  L.control.layers(baseLayers).addTo(map);

  // Step 4: Initialize marker cluster group
  var markers = L.markerClusterGroup();

  // Step 5: Define the custom tree icon
  var treeIcon = L.icon({
    iconUrl: 'assets/img/Tag Icon.png',  // Replace with actual path
    iconSize: [38, 38],  // Size of the icon
    iconAnchor: [19, 38],  // Point of the icon that corresponds to the marker's location
    popupAnchor: [0, -38]  // Point from which the popup should open relative to the iconAnchor
  });

  // Step 6: Fetch plant locations from the server
  fetch('get_plant_data.php')
    .then(response => response.json())
    .then(data => {
      data.forEach(plant => {
        const species_name = plant.species_name;
        const lat = plant.latitude;
        const lon = plant.longitude;
        const address = plant.address; // Ensure consistency in field names
        const imageUrl = plant.image;
        const date_time = plant.date_time; // Capture date from EXIF metadata

       // Step 7: Add markers with custom icon to the marker cluster group
var marker = L.marker([lat, lon], { icon: treeIcon })
.bindPopup(`
<div class="col-4">
    <img src="${plant.image_path}" alt="Plant Image" class="img-fluid" 
         style="width: 150px; height: auto; cursor: pointer;" 
         onclick="openFullscreen('${plant.image_path}')" 
         onerror="this.onerror=null; this.src='assets/img/default-plant.jpg';">
</div>
<strong>Species:</strong> ${species_name}<br>
<strong>Location:</strong> ${address}<br>
<strong>Date Captured:</strong> ${date_time}
`);


        markers.addLayer(marker); // Add each marker to the marker cluster group
      });

      // Add all markers to the map
      map.addLayer(markers);
    })
    .catch(error => console.error('Error fetching plant data:', error));
});

// Function to open image in full screen
function openFullscreen(imageSrc) {
  // Create a new image element
  var img = new Image();
  img.src = imageSrc;
  img.style.position = 'fixed';
  img.style.top = '0';
  img.style.left = '0';
  img.style.width = '100%';
  img.style.height = '100%';
  img.style.objectFit = 'contain';
  img.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
  img.style.cursor = 'pointer';
  img.style.zIndex = '9999';

  // Add the image to the document body
  document.body.appendChild(img);

  // Close the image on click
  img.onclick = function () {
    document.body.removeChild(img);
  };
}


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
</body>
  <script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <script src="assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>
</html>

