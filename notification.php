<?php
session_start();

// Connect to PostgreSQL database
$host = "localhost";
$port = "5432";
$dbname = "evacuation";
$user = "postgres";
$password = "RootAdmin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
    exit;
}

// Check if user is logged in and user_id is set in session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'User ID not set in session'));
    exit;
}

// Fetch buildings for the current user
$userId = $_SESSION['user_id'];
$sql = "SELECT building_name, building_address, building_id FROM user_building WHERE user_id = $userId";
$result = pg_query($conn, $sql);

if (!$result) {
    echo json_encode(array('success' => false, 'message' => 'Error fetching buildings'));
    exit;
}

$buildings = array();
while ($row = pg_fetch_assoc($result)) {
    $buildings[] = array(
        'name' => $row['building_name'],
        'address' => $row['building_address'],
        'id' => $row['building_id']
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Page</title>
    <link rel="stylesheet" href="stylen.css">
</head>
<body>
    <div class="menu-bar">
        <ul>
            <li><a href="selected_building.php">Selected Buildings</a></li>
            <li><a href="user_buildings.php">Buildings</a></li>
            <li><a href="user_support.html">Help Center</a></li>
            <li><button type="button" onclick="getSelectedBuildings()">Notification</button></li>
        </ul>
    </div>

    <div class="buildings-container">
        <h2>Buildings</h2>
        <ul>
            <?php foreach ($buildings as $building): ?>
                <li><a href="#" onclick="getBuildingNodes(<?php echo $building['id']; ?>)"><?php echo $building['name'] . ' - ' . $building['address']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="nodes-container">
        <h2 id="nodeHeading" style="display: none;">Node Names</h2>
        <ul id="nodeList"></ul>
    </div>

    <!-- Add a hidden form to capture the initial node selection -->
    <form id="initialNodeForm" method="POST" action="generate_evacuation_plan.php" style="display: none;">
        <input type="hidden" id="initialNodeInput" name="initialNode">
        <input type="hidden" id="selectedBuildingId" name="selected_building_id">
        <button type="submit" id="submitButton">Submit</button>
    </form>

    <script>
        function getSelectedBuildings() {
            // Your AJAX request code here
        }
        function getBuildingNodes(buildingId) {
    console.log('Building ID:', buildingId); // Debugging output
    document.getElementById('selectedBuildingId').value = buildingId;
    fetch('get_building_nodes.php?building_id=' + parseInt(buildingId, 10))
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data); // Debugging output
        if (data.success) {
            const nodeList = document.getElementById('nodeList');
            const nodeHeading = document.getElementById('nodeHeading');

            nodeHeading.style.display = 'block'; // Show the node names heading
            nodeList.innerHTML = ''; // Clear previous nodes

            data.nodes.forEach(node => {
                console.log('Node:', node); // Debugging output
                const nodeItem = document.createElement('li');
                const nodeLink = document.createElement('a');
                nodeLink.href = 'javascript:void(0);'; // Use javascript:void(0); for click event
                nodeLink.textContent = node; // Use node directly instead of node.name

                // Add click event listener to each node link
                nodeLink.addEventListener('click', () => {
                    console.log('Node clicked:', node); // Debugging output
                    document.getElementById('initialNodeInput').value = node; // Set the initial node value
                    document.getElementById('selectedBuildingId').value = buildingId;
                    document.getElementById('initialNodeForm').submit(); // Submit the form
                });

                nodeItem.appendChild(nodeLink);
                nodeList.appendChild(nodeItem);
            });
        } else {
            alert('Error fetching building nodes: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching building nodes');
    });
}

    </script>
</body>
</html>
