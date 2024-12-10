<?php
header('Content-Type: application/json');

include 'db_connection.php';


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `tree_planted`"; 
$result = $conn->query($sql);

$plants = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $plants[] = $row; 
    }
}

$conn->close();

echo json_encode($plants);
?>
