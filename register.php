<?php
session_start();

// Check if owner is logged in
if (!isset($_SESSION['owner_id'])) {
    die(json_encode(array('success' => false, 'message' => 'Owner not logged in.')));
}

// Retrieve owner_id from session
$owner_id = $_SESSION['owner_id'];

// Retrieve form data
$building_name = isset($_POST['building_name']) ? $_POST['building_name'] : '';
$building_address = isset($_POST['building_address']) ? $_POST['building_address'] : '';

// Check if file uploads are set
if (!isset($_FILES['floorPlan']['tmp_name']) || !isset($_FILES['graph']['tmp_name'])) {
    die(json_encode(array('success' => false, 'message' => 'Floor plan and graph files are required.')));
}

// File upload paths
$uploadsDir = 'uploads/';
$floorPlanFile = $uploadsDir . basename($_FILES['floorPlan']['name']);
$graphFile = $uploadsDir . basename($_FILES['graph']['name']);

// Move uploaded files to the server
if (!move_uploaded_file($_FILES['floorPlan']['tmp_name'], $floorPlanFile) || !move_uploaded_file($_FILES['graph']['tmp_name'], $graphFile)) {
    die(json_encode(array('success' => false, 'message' => 'Error uploading files')));
}

try {
    // Prepare and execute SQL statement to insert building data
    $host = "localhost";
    $port = "5432";
    $dbname = "evacuation";
    $user = "postgres";
    $db_password = "RootAdmin";

    // Establish database connection
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$db_password");

    if (!$conn) {
        die(json_encode(array('success' => false, 'message' => 'Database connection failed')));
    }

    $sql = "INSERT INTO buildings (owner_id, building_name, building_address, floor_plan, graph) 
            VALUES ($1, $2, $3, $4, $5) RETURNING building_id";

    $result = pg_query_params($conn, $sql, array($owner_id, $building_name, $building_address, $floorPlanFile, $graphFile));

    if ($result) {
        // Fetch the newly inserted building ID
        $row = pg_fetch_assoc($result);
        $building_id = $row['building_id'];

        // Store building_id in session
        $_SESSION['building_id'] = $building_id;

        // Close the database connection
        pg_close($conn);

        // Return success response
        echo json_encode(array('success' => true, 'message' => 'Building registered successfully', 'building_id' => $building_id));
    } else {
        // Return error response if insertion fails
        echo json_encode(array('success' => false, 'message' => 'Error registering building'));
    }
} catch (Exception $e) {
    // Log error
    error_log("Error: " . $e->getMessage(), 3, "error.log");
    // Return error response
    echo json_encode(array('success' => false, 'message' => 'An error occurred. Please try again later.'));
}
?>
