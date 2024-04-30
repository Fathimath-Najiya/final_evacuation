<?php
// Database connection settings
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$password = "RootAdmin";

// Connect to PostgreSQL database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

// Insert data into database
$sql = "INSERT INTO public.users (username, password) VALUES ('$username', '$password')";

// Execute SQL query
if (pg_query($conn, $sql)) {
    echo "Signup successful";
} else {
    // Log error message
    error_log("Error: " . $sql . " - " . pg_last_error($conn));
    echo "Error occurred. Please try again.";
}

// Close database connection
pg_close($conn);
?>
