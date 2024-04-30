<?php
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO public.users (username, password) VALUES ($1, $2)";
$result = pg_query_params($conn, $sql, array($username, $hashedPassword));

if ($result) {
    $response = array('success' => true, 'message' => 'Signup successful');
   
    echo json_encode($response);

    // Redirect to login page
    header("Location: login.html");
    exit();
} else {
    $errorMessage = 'Error: ' . pg_last_error($conn);
    
    error_log("Username: $username, Password: $hashedPassword, Error: $errorMessage", 3, "error.log");
    
    $response = array('success' => false, 'message' => $errorMessage);
}

echo json_encode($response);
//print_r($response);

// Close database connection
pg_close($conn);
?>