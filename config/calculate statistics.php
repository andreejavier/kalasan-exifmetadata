<?php
// Database connection details
$host = 'localhost';
$dbname = '';
$username = 'root';
$password = 'your_db_password';

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to fetch tree species data
    function fetchTreeSpeciesData($pdo) {
        // SQL query to select all data from tree_species
        $stmt = $pdo->prepare("SELECT * FROM plants");
        $stmt->execute();
        
        // Fetch all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch the data
    $treeSpeciesData = fetchTreeSpeciesData($pdo);

    // Calculate statistics
    $totalUploads = count($treeSpeciesData);  // Total number of uploads
    $uniqueSpecies = count(array_unique(array_column($treeSpeciesData, 'address')));  // Unique tree species (using 'address' as proxy for species name)

    // Encode the data and statistics into JSON to pass it to JavaScript
    echo "<script>
            var treeSpeciesData = " . json_encode($treeSpeciesData) . ";
            var totalUploads = $totalUploads;
            var uniqueSpecies = $uniqueSpecies;
          </script>";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
