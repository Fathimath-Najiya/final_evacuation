<?php
session_start();

// Database connection (replace with your actual credentials)
$host = 'localhost';
$port = '5432';
$username = 'postgres';
$password = 'RootAdmin';
$database = 'evacuation';

$conn = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

if (isset($_POST['numberOfNodes'])) {
    $buildingId = $_SESSION['building_id']; // Assuming you have set 'building_id' in session

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'distance_') !== false) {
            // Extract node_from and node_to from input name
            $nodeNames = explode('_', substr($key, 9)); // 9 is the length of 'distance_'
            $nodeFrom = $nodeNames[0];
            $nodeTo = $nodeNames[2];
            $distance = $value;

            // Insert distance into node_distance table
            $sqlInsertDistance = "INSERT INTO node_distance (building_id, node_from, node_to, distance) 
                                  VALUES ('$buildingId', '$nodeFrom', '$nodeTo', '$distance')";

            if (pg_query($conn, $sqlInsertDistance)) {
                echo "Distance from Node $nodeFrom to Node $nodeTo inserted successfully.<br>";
            } else {
                echo "Error inserting distance: " . pg_last_error($conn) . "<br>";
            }
        }
    }
}

// Close the connection
pg_close($conn);
?>