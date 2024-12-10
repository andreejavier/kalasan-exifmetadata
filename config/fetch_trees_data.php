<?php
// Database connection
include 'db_connection.php';

$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate tree species data
function validateTreeSpecies($species) {
    if (empty($species['image'])) {
        return 'Image is required.';
    }
    if (!is_numeric($species['latitude']) || $species['latitude'] < -90 || $species['latitude'] > 90) {
        return 'Latitude must be a number between -90 and 90.';
    }
    if (!is_numeric($species['longitude']) || $species['longitude'] < -180 || $species['longitude'] > 180) {
        return 'Longitude must be a number between -180 and 180.';
    }
    if (empty($species['date_time'])) {
        return 'Date and time is required.';
    }
    if (empty($species['address'])) {
        return 'Address is required.';
    }
    return true;
}

// Fetch tree species data from the database
$sql = "SELECT * FROM newtree_species";
$result = $conn->query($sql);

$treeSpeciesData = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $treeSpeciesData[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="./assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Kalasan Analytics Dashboard</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!-- CSS Files -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
</head>

<body>
    <div class="wrapper">
        <div class="sidebar" data-color="white">
            <div class="logo">
                <a href="#" class="simple-text logo-normal">Kalasan</a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="active">
                        <a href="javascript:;">
                            <i class="nc-icon nc-bank"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel" style="height: 100vh;">
            <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:;">Kalasan Analytics</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="description">Tree Species Data</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Date and Time</th>
                                    <th>Address</th>
                                    <th>User ID</th>
                                    <th>Uploaded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($treeSpeciesData as $species) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($species['id']) ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars($species['image']) ?>" alt="Tree Image" style="width: 100px; height: auto;">
                                        </td>
                                        <td><?= htmlspecialchars($species['latitude']) ?></td>
                                        <td><?= htmlspecialchars($species['longitude']) ?></td>
                                        <td><?= htmlspecialchars($species['date_time']) ?></td>
                                        <td><?= htmlspecialchars($species['address']) ?></td>
                                        <td><?= htmlspecialchars($species['user_id']) ?></td>
                                        <td><?= htmlspecialchars($species['uploaded_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="credits ml-auto">
                            <span class="copyright">© 2024, Kalasan Project</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
