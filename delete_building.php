<?php
session_start();

// Connect to your database (replace these variables with your actual database credentials)
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$db_password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$db_password");

if (!$conn) {
    die(json_encode(array('success' => false, 'message' => 'Database connection failed')));
}

// Retrieve the building ID from the AJAX request
$data = json_decode(file_get_contents("php://input"));
$buildingId = $data->buildingId;

// Perform the delete operation
$query = "DELETE FROM public.buildings WHERE building_id = $1";
$result = pg_query_params($conn, $query, array($buildingId));

if ($result) {
    echo json_encode(array('success' => true, 'message' => 'Building deleted successfully', 'buildingId' => $buildingId));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error deleting building'));
}

// Close the database connection
pg_close($conn);
?>
