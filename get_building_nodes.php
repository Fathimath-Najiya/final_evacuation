<?php
// Connect to your database (assuming you already have a connection)
session_start();

// Connect to your database
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$db_password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$db_password");

if (!$conn) {
    echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
    exit;
}

if (isset($_GET['building_id'])) {
    $buildingId = $_GET['building_id'];
    
    // Fetch node names based on building ID
    $sql = "SELECT node_name FROM graph_data WHERE building_id = $buildingId";
    $result = pg_query($conn, $sql);

    if (!$result) {
        echo json_encode(array('success' => false, 'message' => 'Error fetching nodes'));
        exit;
    }

    $nodes = array();
    while ($row = pg_fetch_assoc($result)) {
        $nodes[] = $row['node_name'];
    }

    echo json_encode(array('success' => true, 'nodes' => $nodes));
    exit;
} else {
    echo json_encode(array('success' => false, 'message' => 'Invalid request'));
    exit;
}
?>
