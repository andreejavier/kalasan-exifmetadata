<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    error_log("Session user_id not set. Redirecting to login.");
    header("Location: index.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Plant Capture</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <style>
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
        <li class="active">
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
                    <a class="navbar-brand" href="javascript:;">Plant</a>
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

        <!-- Main Panel Starts -->
     
         
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img id="imagePreview" class="img-fluid" src="https://www.freeiconspng.com/uploads/no-image-icon-6.png" alt="Preview">
                        </div>
                        <div class="col-md-8">
                            <h5 class="card-title">Upload</h5>
                            <!-- Camera Input -->
                            <input type="file" id="imageInputCamera" class="d-none" accept="image/*" capture="environment" onchange="handleImageInput(this);" />
                            <button class="btn btn-primary mt-3" onclick="getGeolocationAndOpenCamera();">Open Camera</button>

                            <!-- Gallery Input -->
                            <input type="file" id="imageInputGallery" class="d-none" accept="image/*" onchange="handleImageInput(this);" />
                            <button class="btn btn-secondary mt-3" onclick="document.getElementById('imageInputGallery').click();">Open Gallery</button>

                            <form id="plantDataForm" class="mt-3" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="latitudeInput" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitudeInput" name="lat" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="longitudeInput" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitudeInput" name="lon" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="dateTime" class="form-label">Datetime</label>
                                    <input type="text" class="form-control" id="dateTime" name="date" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="locationAddress" class="form-label">Location Address</label>
                                    <input type="text" id="locationAddress" class="form-control" name="address" readonly>
                                </div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Save Data</button>
                                <button id="clearButton" class="btn btn-secondary" type="button" onclick="clearForm()">Clear</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Starts -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Upload Details?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">Do you want to save the data?</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="submitForm()">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Ends -->
            </div>
        </div>
        <!-- Main Panel Ends -->
    </div>

    <!-- JavaScript Libraries and EXIF JS -->
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
    <script>
        // JavaScript functions for image handling and geolocation
        function handleImageInput(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('imagePreview').src = e.target.result;

                    EXIF.getData(file, function () {
                        const lat = EXIF.getTag(this, "GPSLatitude");
                        const lon = EXIF.getTag(this, "GPSLongitude");
                        const dateTime = EXIF.getTag(this, "DateTimeOriginal");

                        if (lat && lon) {
                            const latitude = convertDMSToDD(lat);
                            const longitude = convertDMSToDD(lon);
                            document.getElementById('latitudeInput').value = latitude;
                            document.getElementById('longitudeInput').value = longitude;
                            getAddress(latitude, longitude);
                        }
                        if (dateTime) {
                            document.getElementById('dateTime').value = dateTime;
                        }
                    });
                };
                reader.readAsDataURL(file);
            }
        }

        // Convert DMS to Decimal Degrees for GPS coordinates
        function convertDMSToDD(dms) {
            return dms[0] + dms[1] / 60 + dms[2] / 3600;
        }

// Function to retrieve address via reverse geocoding
function getAddress(latitude, longitude) {
    const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json&zoom=18`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('locationAddress').value = data.display_name || 'Address not available';
        })
        .catch(error => {
            console.error("Error fetching address:", error);
            document.getElementById('locationAddress').value = "Unable to retrieve address";
        });
}

// Convert DMS to Decimal Degrees for GPS coordinates
function convertDMSToDD(dms) {
    const degrees = dms[0] || 0;
    const minutes = dms[1] || 0;
    const seconds = dms[2] || 0;
    return degrees + minutes / 60 + seconds / 3600;
}

// Geolocation API to get current position and then open camera
function getGeolocationAndOpenCamera() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            document.getElementById('latitudeInput').value = latitude;
            document.getElementById('longitudeInput').value = longitude;
            getAddress(latitude, longitude);

            // Open the camera after geolocation
            document.getElementById('imageInputCamera').click();
        }, function(error) {
            console.log("Geolocation error code: " + error.code);
            console.log("Geolocation error message: " + error.message);
            alert("Geolocation failed: " + error.message);
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}


function submitForm() {
    const form = document.getElementById('plantDataForm');
    const formData = new FormData(form);

    // Append user_id to the form data
    formData.append('user_id', <?php echo $_SESSION['user_id']; ?>);

    // Append image file
    const imageInputCamera = document.getElementById('imageInputCamera');
    const imageInputGallery = document.getElementById('imageInputGallery');
    const imageFile = imageInputCamera.files[0] || imageInputGallery.files[0];

    if (!imageFile) {
        alert("No image selected! Please upload an image.");
        return;
    }
    formData.append('image', imageFile);

    // Perform AJAX request
    fetch('save-plant-data.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Plant data saved successfully!");
                form.reset(); // Clear form after successful submission
            } else {
                alert(result.message || "An error occurred. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while saving the data.");
        });
}



        function clearForm() {
            document.getElementById('latitudeInput').value = '';
            document.getElementById('longitudeInput').value = '';
            document.getElementById('dateTime').value = '';
            document.getElementById('locationAddress').value = '';
            document.getElementById('imagePreview').src = 'https://www.freeiconspng.com/uploads/no-image-icon-6.png';
            document.getElementById('imageDataURL').value = '';
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
