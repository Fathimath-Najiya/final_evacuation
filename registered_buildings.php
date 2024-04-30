<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Buildings</title>
    <style>
        .building {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }

        .building-name {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .building-address {
            font-style: italic;
            color: #666;
        }

        .action-button {
            margin-right: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .action-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Your PHP code to display buildings goes here -->
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

    // Fetch buildings registered by the logged-in owner
    $owner_id = $_SESSION['owner_id'];
    $query = "SELECT building_id, building_name, building_address FROM public.buildings WHERE owner_id = $1";
    $result = pg_query_params($conn, $query, array($owner_id));

    if (!$result) {
        die(json_encode(array('success' => false, 'message' => 'Error fetching buildings')));
    }

    // Display the list of registered buildings
    while ($row = pg_fetch_assoc($result)) {
        echo '<div class="building">';
        echo '<p class="building-name">' . $row['building_name'] . '</p>';
        echo '<p class="building-address">' . $row['building_address'] . '</p>';
        echo '<button class="action-button" onclick="updateBuilding(' . $row['building_id'] . ')">Update</button>';
        echo '<button class="action-button" onclick="deleteBuilding(' . $row['building_id'] . ')">Delete</button>';
        echo '</div>';
    }

    // Close the database connection
    pg_close($conn);
    ?>

    <!-- Your JavaScript code for updateBuilding and deleteBuilding functions goes here -->
    <script>
        function updateBuilding(buildingId) {
            // Implement your update logic here
            window.location.href = 'update_building.php';
            console.log('Update building:', buildingId);

        }

        function deleteBuilding(buildingId) {
            if (confirm('Are you sure you want to delete this building?')) {
                // Call deleteBuilding function if confirmed
                console.log('Delete building:', buildingId);
                // Send AJAX request to delete building
                fetch('delete_building.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ buildingId: buildingId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI or display success message
                        console.log('Building deleted successfully:', data.buildingId);
                        // You can update the UI here if needed
                    } else {
                        // Handle error or display error message
                        console.error('Error deleting building:', data.message);
                        // You can display an error message to the user
                    }
                })
                .catch(error => {
                    console.error('Error deleting building:', error);
                    // You can display an error message to the user
                });
            }
        }
    </script>

</body>
</html>
