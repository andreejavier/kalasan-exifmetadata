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
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Starts -->
        <div class="sidebar" data-color="white" data-active-color="danger">
            <div class="logo">
                <a href="#" class="simple-text logo-mini">
                    <img src="assets/img/tree icon.png" alt="Tree Icon">
                </a>
                <a href="#" class="simple-text logo-normal">
                    Kalasan
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="nc-icon nc-bank"></i>
                            <p>Home</p>
                        </a>
                    </li>
                    <li>
                        <a href="./map.php">
                            <i class="nc-icon nc-pin-3"></i>
                            <p>Map</p>
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
        <!-- Sidebar Ends -->

        <!-- Main Panel Starts -->
        <div class="main-panel">
            <div class="content">
                <div class="container mt-4">
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
            const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('locationAddress').value = data.display_name || 'Address not available';
                });
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


        // Clear form data
        function clearForm() {
            document.getElementById('latitudeInput').value = '';
            document.getElementById('longitudeInput').value = '';
            document.getElementById('dateTime').value = '';
            document.getElementById('locationAddress').value = '';
            document.getElementById('imagePreview').src = 'https://www.freeiconspng.com/uploads/no-image-icon-6.png';
        }

        // Submit form function (to be implemented)
        function submitForm() {
            alert("Data submitted!");
        }
    </script>
</body>
</html>
