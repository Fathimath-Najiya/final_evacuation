<?php
// Start the session
session_start();

// Database connection parameters
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

// Check if owner is logged in (you may need to adjust this based on your authentication logic)
if (!isset($_SESSION['user_id'])) {
    die(json_encode(array('success' => false, 'message' => 'User not logged in.')));
}

// Retrieve user_id from session (assuming you have this set in your session)
$user_id = $_SESSION['user_id'];

// Retrieve building_id from the request parameter
$building_id = isset($_GET['building_id']) ? $_GET['building_id'] : null;

if (!$building_id) {
    die(json_encode(array('success' => false, 'message' => 'Building ID not provided.')));
}

// Sanitize input
$building_id = pg_escape_string($conn, $building_id);

// Retrieve building_name and building_address from the buildings table based on building_id
$queryBuildingInfo = "SELECT building_name, building_address FROM public.buildings WHERE building_id = $1";
$resultBuildingInfo = pg_query_params($conn, $queryBuildingInfo, array($building_id));
if (!$resultBuildingInfo) {
    die(json_encode(array('success' => false, 'message' => 'Error fetching building info.')));
}

$rowBuildingInfo = pg_fetch_assoc($resultBuildingInfo);
$building_name = $rowBuildingInfo['building_name'];
$building_address = $rowBuildingInfo['building_address'];

// Check if the building is already in the user's list (optional, depending on your logic)
$queryCheckExistence = "SELECT COUNT(*) FROM public.user_building WHERE building_id = $1 AND user_id = $2";
$resultExistence = pg_query_params($conn, $queryCheckExistence, array($building_id, $user_id));
$rowExistence = pg_fetch_assoc($resultExistence);

if ($rowExistence['count'] > 0) {
    die(json_encode(array('success' => false, 'message' => 'Building already exists in your list.')));
}

// Insert data into user_building table
$queryInsert = "INSERT INTO public.user_building (building_id, building_name, building_address, user_id) 
                VALUES ($1, $2, $3, $4)";
$resultInsert = pg_query_params($conn, $queryInsert, array($building_id, $building_name, $building_address, $user_id));
if ($resultInsert) {
    echo json_encode(array('success' => true, 'message' => 'Building added successfully'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error adding building to user list.'));
}

// Close the connection
pg_close($conn);
?>
