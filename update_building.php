<?php
session_start();

// Connect to your database
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$db_password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$db_password");

if (!$conn) {
    die(json_encode(array('success' => false, 'message' => 'Database connection failed')));
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        if (isset($_POST['oldName'], $_POST['newName'])) {
            $oldName = pg_escape_string($_POST['oldName']);
            $newName = pg_escape_string($_POST['newName']);

            // Update building_name in the buildings table
            $sqlUpdateBuilding = "UPDATE buildings SET building_name = $1, building_address = $2 WHERE building_name = $3";
            $resultUpdateBuilding = pg_query_params($conn, $sqlUpdateBuilding, array($newName, $_POST['building_address'], $oldName));

            if (!$resultUpdateBuilding) {
                die(json_encode(array('success' => false, 'message' => 'Error updating building details')));
            }

            // Update graph data if provided
            if (isset($_POST['node_name'], $_POST['coordinate_x'], $_POST['coordinate_y'], $_POST['heuristic_value'])) {
                $nodeNames = $_POST['node_name'];
                $coordinateX = $_POST['coordinate_x'];
                $coordinateY = $_POST['coordinate_y'];
                $heuristicValue = $_POST['heuristic_value'];

                // Assuming the building ID is stored in the session
                $buildingId = $_SESSION['building_id'];

                for ($i = 0; $i < count($nodeNames); $i++) {
                    $nodeName = pg_escape_string($nodeNames[$i]);
                    $coordX = pg_escape_string($coordinateX[$i]);
                    $coordY = pg_escape_string($coordinateY[$i]);
                    $heuristic = pg_escape_string($heuristicValue[$i]);

                    // Check if node exists for this building ID
                    $checkQuery = "SELECT * FROM graph_data WHERE building_id = $1 AND node_name = $2";
                    $checkResult = pg_query_params($conn, $checkQuery, array($buildingId, $nodeName));
                    $existingData = pg_fetch_assoc($checkResult);

                    if ($existingData) {
                        // Node exists, update the data
                        $updateColumns = array();
                        if (!empty($coordX) && $coordX != '#') {
                            $updateColumns[] = "coordinate_x = $coordX";
                        }
                        if (!empty($coordY) && $coordY != '#') {
                            $updateColumns[] = "coordinate_y = $coordY";
                        }
                        if (!empty($heuristic) && $heuristic != '#') {
                            $updateColumns[] = "heuristic_value = $heuristic";
                        }

                        if (!empty($updateColumns)) {
                            $updateQuery = "UPDATE graph_data SET " . implode(", ", $updateColumns) . " WHERE building_id = $1 AND node_name = $2";
                            $updateResult = pg_query_params($conn, $updateQuery, array($buildingId, $nodeName));

                            if (!$updateResult) {
                                die(json_encode(array('success' => false, 'message' => 'Error updating graph data')));
                            }
                        }
                    } else {
                        // Node doesn't exist, do nothing or handle as needed
                    }
                }
            }

            echo json_encode(array('success' => true, 'message' => 'Building details updated successfully'));
            exit; // Prevent further execution
        }
    }
}

// Display the form to update building details
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Building</title>
    <link rel="stylesheet" href="styleup.css">
    <style>
        .para p {
    color: #a09a9a; /* light grey color */
    font-size: small; /* smaller font size */
    font-style: italic; /* italic font style */
}
</style>
    
</head>
<body>
    <h2>Update Building Details</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="oldName">Old Name:</label>
        <input type="text" id="oldName" name="oldName">

        <label for="newName">New Name:</label>
        <input type="text" id="newName" name="newName">
      
        <label for="building_address">Building Address:</label>
        <input type="text" id="building_address" name="building_address">
        <!-- Add fields for graph data -->
       <div class="para"> <p>If you do not want to change a particular value, enter "#" in that field.</p></div>
        <label for="numberOfNodes">Number of Nodes:</label>
        <input type="number" id="numberOfNodes" name="numberOfNodes" required>

        <button type="button" onclick="generateFields()" style="margin-bottom: 10px;">Generate Fields</button>

        <div id="nodeFields"></div>
        <div style="text-align: center;">
        <button type="submit" name="submit">Update Building</button>
</div>
    </form>

    <script>
        function generateFields() {
            var numberOfNodes = document.getElementById('numberOfNodes').value;
            var nodeFields = document.getElementById('nodeFields');
            nodeFields.innerHTML = '';

            for (var i = 1; i <= numberOfNodes; i++) {
                var nodeDiv = document.createElement('div');
                nodeDiv.classList.add('node-container');

                // Node Name
                var nameLabel = document.createElement('label');
                nameLabel.textContent = 'Node ' + i + ' Name:';
                var nameInput = document.createElement('input');
                nameInput.type = 'text';
                nameInput.name = 'node_name[]'; // Use array notation for multiple inputs
                nameInput.required = true;
                nodeDiv.appendChild(nameLabel);
                nodeDiv.appendChild(nameInput);

                // Coordinate X
                var coordinateLabelX = document.createElement('label');
                coordinateLabelX.textContent = 'Node ' + i + ' Coordinate X:';
                var coordinateInputX = document.createElement('input');
                coordinateInputX.type = 'text';
                coordinateInputX.name = 'coordinate_x[]'; // Use array notation for multiple inputs
                coordinateInputX.required = true;
                nodeDiv.appendChild(coordinateLabelX);
                nodeDiv.appendChild(coordinateInputX);

                // Coordinate Y
                var coordinateLabelY = document.createElement('label');
                coordinateLabelY.textContent = 'Node ' + i + ' Coordinate Y:';
                var coordinateInputY = document.createElement('input');
                coordinateInputY.type = 'text';
                coordinateInputY.name = 'coordinate_y[]'; // Use array notation for multiple inputs
                coordinateInputY.required = true;
                nodeDiv.appendChild(coordinateLabelY);
                nodeDiv.appendChild(coordinateInputY);

                // Heuristic Value
                var heuristicLabel = document.createElement('label');
                heuristicLabel.textContent = 'Heuristic Value for Node ' + i + ':';
                var heuristicInput = document.createElement('input');
                heuristicInput.type = 'number';
                heuristicInput.name = 'heuristic_value[]'; // Use array notation for multiple inputs
                heuristicInput.required = true;
                nodeDiv.appendChild(heuristicLabel);
                nodeDiv.appendChild(heuristicInput);
                nodeFields.appendChild(nodeDiv);
            }
        }
    </script>
</body>
</html>
