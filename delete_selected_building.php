<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(array('success' => false, 'message' => 'User not logged in.')));
}

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

// Get the building_id to be deleted from the request parameter
$building_id = isset($_GET['building_id']) ? $_GET['building_id'] : '';

// Check if building_id is provided
if (!$building_id) {
    die(json_encode(array('success' => false, 'message' => 'Building ID is required.')));
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Delete the building from user_building table
$query = "DELETE FROM public.user_building WHERE building_id = $1 AND user_id = $2";
$result = pg_query_params($conn, $query, array($building_id, $user_id));
if ($result) {
    echo json_encode(array('success' => true, 'message' => 'Building deleted successfully'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error deleting building'));
}

// Close the connection
pg_close($conn);
?>
