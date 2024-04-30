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
    echo "Failed to connect to the database.";
    exit;
}

if (!isset($_POST['selected_building_id'])) {
    echo "Error: selected_building_id not provided.";
    exit;
}
$_SESSION['selected_building_id'] = $_POST['selected_building_id'];
$selectedBuildingId = $_SESSION['selected_building_id'];

// Retrieve graph data from the database based on the selected building ID
$graph = [];
$sqlGraph = "SELECT node_from, node_to, distance FROM node_distance WHERE building_id = '$selectedBuildingId'";
$resultGraph = pg_query($conn, $sqlGraph);
while ($row = pg_fetch_assoc($resultGraph)) {
    $fromNode = $row['node_from'];
    $toNode = $row['node_to'];
    $distance = intval($row['distance']);
    $graph[$fromNode][$toNode] = $distance;
}

// Retrieve heuristic values from the database based on the selected building ID
$heuristic = [];
$sqlHeuristic = "SELECT node_name, heuristic_value FROM graph_data WHERE building_id = '$selectedBuildingId'";
$resultHeuristic = pg_query($conn, $sqlHeuristic);
while ($row = pg_fetch_assoc($resultHeuristic)) {
    $nodeName = $row['node_name'];
    $heuristicValue = intval($row['heuristic_value']);
    $heuristic[$nodeName] = $heuristicValue;
}

// Query to find the goal node for the selected building
$sqlGoalNode = "SELECT node_name FROM graph_data WHERE building_id = '$selectedBuildingId' AND goal_node = 'Y'";
$resultGoalNode = pg_query($conn, $sqlGoalNode);
if ($rowGoalNode = pg_fetch_assoc($resultGoalNode)) {
    $goalNode = $rowGoalNode['node_name'];
} else {
    echo "Error: No goal node found.";
    exit;
}

function astarWithIndirectPaths($graph, $heuristic, $start, $goal) {
    if (!isset($graph[$start]) || !isset($graph[$goal])) {
        return null; // Return null if either the start or goal node is not in the graph
    }
  
    $openList = new SplPriorityQueue();
    $openList->insert($start, 0);
    $closedList = [];
    $gScore = array_fill_keys(array_keys($graph), PHP_INT_MAX);
    $gScore[$start] = 0;
    $cameFrom = [];

    while (!$openList->isEmpty()) {
        $current = $openList->extract();
        
        if ($current == $goal) {
            $path = [];
            while (isset($cameFrom[$current])) {
                array_unshift($path, $current);
                $current = $cameFrom[$current];
            }
            array_unshift($path, $start);
            return $path; // Return the valid path found
        }

        $closedList[$current] = true;

        foreach ($graph[$current] as $neighbor => $distance) {
            if (isset($closedList[$neighbor])) {
                continue; // Skip neighbors already evaluated
            }

            $tentativeGScore = $gScore[$current] + $distance;
            if ($tentativeGScore < $gScore[$neighbor]) {
                $cameFrom[$neighbor] = $current;
                $gScore[$neighbor] = $tentativeGScore;
                $fScore = $gScore[$neighbor] + (isset($heuristic[$neighbor]) ? $heuristic[$neighbor] : PHP_INT_MAX);
                $openList->insert($neighbor, -$fScore);
            }
        }
    }
    return null; // Return null if no valid path is found
}

if (isset($_POST['initialNode'])) {
    $initialNode = $_POST['initialNode'];
    $path = astarWithIndirectPaths($graph, $heuristic, $initialNode, $goalNode);
    if ($path !== null) {
        echo "Path found: " . implode(' -> ', $path) . "\n";
    } else {
        echo "Error: No valid path found from $initialNode to $goalNode.";
    }
} else {
    echo "Error: Initial node not specified.";
}

$node_coordinates = [];
$sqlCoordinates = "SELECT node_name, coordinate_x, coordinate_y FROM graph_data WHERE building_id = '$selectedBuildingId'";
$resultCoordinates = pg_query($conn, $sqlCoordinates);
while ($row = pg_fetch_assoc($resultCoordinates)) {
    $nodeName= $row['node_name'];
    $xCoordinate = $row['coordinate_x'];
    $yCoordinate = $row['coordinate_y'];
    // $node_coordinates[$nodeName][0] = $xCoordinate;
    // $node_coordinates[$nodeName][1] = $yCoordinate;
    $node_coordinates[$nodeName] = ['x' => $xCoordinate, 'y' => $yCoordinate];
}

$directions = [];
for ($i = 0; $i < count($path) - 1; $i++) {
    $current_node = $path[$i];
    $next_node = $path[$i + 1];
    $current_x = $node_coordinates[$current_node]['x'];
    $current_y = $node_coordinates[$current_node]['y'];
    $next_x = $node_coordinates[$next_node]['x'];
    $next_y = $node_coordinates[$next_node]['y'];
    $dx = $next_x - $current_x;
    $dy = $next_y - $current_y;

    if ($dx > 0 && $dy > 0) {
        $direction = "Move Slight right forward";
    } elseif ($dx > 0 && $dy < 0) {
        $direction = "Move Slight right backward";
    } elseif ($dx > 0) {
        $direction = "Move right";
    } elseif ($dx < 0 && $dy > 0) {
        $direction = "Move Slight left forward";
    } elseif ($dx < 0 && $dy < 0) {
        $direction = "Move Slight left backward";
    } elseif ($dx < 0) {
        $direction = "Move left";
    } elseif ($dy > 0) {
        $direction = "Move forward";
    } elseif ($dy < 0) {
        $direction = "Move backward";
    } else {
        $direction = "Stay in place";
    }

    $directions[] = $direction;
}

// Print or use $directions as needed
print_r($directions);
?>
