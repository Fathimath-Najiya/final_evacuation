<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
    <style>
        /* Styles for building list */
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

        .delete-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    
    <h3>Your Buildings:</h3>
    <div id="buildingList">
        <?php
            // Start the session
            session_start();

            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                die("User not logged in.");
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
                die("Database connection failed");
            }

            // Retrieve user_id from session
            $user_id = $_SESSION['user_id'];

            // Fetch buildings added by the user
            $query = "SELECT b.building_id, b.building_name, b.building_address 
                      FROM buildings b
                      JOIN user_building ub ON b.building_id = ub.building_id
                      WHERE ub.user_id = $1";
            $result = pg_query_params($conn, $query, array($user_id));
            if ($result) {
                while ($row = pg_fetch_assoc($result)) {
                    echo '<div class="building">';
                    echo '<p class="building-name">' . $row['building_name'] . '</p>';
                    echo '<p class="building-address">' . $row['building_address'] . '</p>';
                    echo '<button class="delete-button" data-building-id="' . $row['building_id'] . '">Delete</button>';
                    echo '</div>';
                }
            } else {
                echo "Error fetching buildings";
            }

            // Close the connection
            pg_close($conn);
        ?>
    </div>

    <script>
        // Function to handle building deletion
        function deleteBuilding(buildingId) {
            // Show confirmation dialog
            if (confirm('Are you sure you want to delete this building?')) {
                // Send AJAX request to delete_building.php with buildingId parameter
                fetch('delete_selected_building.php?building_id=' + buildingId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Building deleted successfully');
                        // Reload the page to update the building list
                        window.location.reload();
                    } else {
                        alert('Error deleting building: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting building');
                });
            }
        }

        // Add event listener to handle click on Delete button
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const buildingId = this.getAttribute('data-building-id');
                    deleteBuilding(buildingId);
                });
            });
        });
    </script>
</body>
</html>
