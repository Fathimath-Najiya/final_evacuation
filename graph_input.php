<?php
// graph_input.php

// Start the session (if not already started)
session_start();

// Database connection (replace with your actual credentials)
$host = 'localhost';
$port = '5432'; // Default PostgreSQL port
$username = 'postgres';
$password = 'RootAdmin';
$database = 'evacuation';

// Create a connection
$conn = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

if (isset($_POST['numberOfNodes'])) {
    $numberOfNodes = $_POST['numberOfNodes'];
    $buildingId = $_SESSION['building_id'];
    $nodeNames = []; // Array to hold node names

    // Loop through each node
    for ($i = 1; $i <= $numberOfNodes; $i++) {
        $nodeName = $_POST['node_name' . $i];
        $coordinateX = $_POST['coordinate_x' . $i];
        $coordinateY = $_POST['coordinate_y' . $i];
        $heuristicValue = $_POST['heuristic_value' . $i];
        $goalNode = $_POST['goal_node' . $i];
        // Store node name in the array
        $nodeNames[$i] = $nodeName;

        // Insert data into the graph_data table
        $sqlGraphData = "INSERT INTO graph_data (building_id, node_name, coordinate_x, coordinate_y, heuristic_value,goal_node) VALUES ('$buildingId', '$nodeName', '$coordinateX', '$coordinateY', '$heuristicValue','$goalNode')";
        if (pg_query($conn, $sqlGraphData)) {
            echo "Node $nodeName details inserted successfully into graph_data.<br>";
        } else {
            echo "Error: " . pg_last_error($conn) . "<br>";
        }
    }

    // Loop again to insert distances with node names
    for ($i = 1; $i <= $numberOfNodes; $i++) {
        for ($j = 1; $j <= $numberOfNodes; $j++) {
            if ($j !== $i) {
                $distanceValue = $_POST['distanceFromNode_' . $i . '_ToNode_' . $j];
                if ($distanceValue == 0) {
                    $distanceValue = 100000;
                }
                $sqlNodeDistance = "INSERT INTO node_distance (building_id, node_from, node_to, distance) VALUES ('$buildingId', '{$nodeNames[$i]}', '{$nodeNames[$j]}', '$distanceValue')";
                if (pg_query($conn, $sqlNodeDistance)) {
                    echo "Distance from Node {$nodeNames[$i]} to Node {$nodeNames[$j]} inserted successfully into node_distance.<br>";
                } else {
                    echo "Error: " . pg_last_error($conn) . "<br>";
                }
            }
        }
    }
}

// Close the connection
pg_close($conn);
?>
