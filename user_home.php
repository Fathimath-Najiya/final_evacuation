<?php
// Connect to the database
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Database connection failed");
}

// Query to retrieve registered buildings
$query = "SELECT * FROM buildings";
$result = pg_query($conn, $query);

if (!$result) {
    die("Error fetching buildings");
}

// Display registered buildings
while ($row = pg_fetch_assoc($result)) {
    echo "<div class='building'>";
    echo "<p>Building Name: " . $row['building_name'] . "</p>";
    echo "<p>Building Address: " . $row['building_address'] . "</p>";
    echo "<button class='add-button' onclick='addBuilding(" . $row['building_id'] . ")'>Add</button>";
    echo "</div>";
}

// Close the database connection
pg_close($conn);
?>