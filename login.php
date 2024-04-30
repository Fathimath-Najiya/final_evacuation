<?php
header('Content-Type: application/json');

session_start();

$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$db_password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$db_password");

if (!$conn) {
    echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
    exit; // Terminate script execution after sending the response
}

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

try {
    // Validate inputs
    if (empty($username) || empty($password)) {
        throw new Exception('Ownername and password are required fields');
    }

    // Prepare SQL statement with parameterized query
    $sql = "SELECT * FROM public.users WHERE username = $1";
    $stmt = pg_prepare($conn, "login_query", $sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare SQL statement');
    }

    // Execute the prepared statement with parameters
    $result = pg_execute($conn, "login_query", array($username));

    // Check if the query execution was successful
    if (!$result) {
        throw new Exception('Query execution failed');
    }

    // Check if any rows were returned
    if (pg_num_rows($result) > 0) {
        // Fetch the row
        $row = pg_fetch_assoc($result);
        
        // Verify the username and password
        if (password_verify($password, $row['password'])) {
            // Username and password match, login successful
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['user_id']; // Assuming 'id' is the column name for user_id in the owners table

            echo json_encode(array('success' => true, 'message' => 'Login successful'));
        } else {
            // Incorrect username or password
            echo json_encode(array('success' => false, 'message' => 'Incorrect username or password'));
        }
    } else {
        // User with the provided username not found
        echo json_encode(array('success' => false, 'message' => 'Owner not found'));
    }
} catch (Exception $e) {
    // Log error
    error_log("Error: " . $e->getMessage(), 3, "error.log");
    // Return error response
    echo json_encode(array('success' => false, 'message' => 'An error occurred. Please try again later.'));
}

// Close database connection
pg_close($conn);
?>
