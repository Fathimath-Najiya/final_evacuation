<?php
// Sample graph resembling a floor plan
$graph = [
    'A' => ['B' => 5, 'C' => 3],
    'B' => ['D' => 4, 'E' => 10, 'C' => 2],
    'C' => ['F' => 2, 'G' => 4],
    'D' => ['H' => 2],
    'E' => ['I' => 5],
    'F' => ['I' => 3],
    'G' => ['H' => 6],
    'H' => ['I' => 5],
    'I' => []
];

// Heuristic function (straight-line distance)
$heuristic = [
    'A' => 7, 'B' => 6, 'C' => 5,
    'D' => 4, 'E' => 4, 'F' => 3,
    'G' => 3, 'H' => 2, 'I' => 0
];

// Coordinates of nodes
$nodeCoordinates = [
    'A' => [0, 0], 'B' => [1, 0], 'C' => [2, 0], 'D' => [3, 0],
    'E' => [0, 1], 'F' => [1, 1], 'G' => [2, 1], 'H' => [3, 1],
    'I' => [0, 2], 'J' => [1, 2], 'K' => [2, 2], 'L' => [3, 2],
    'M' => [0, 3], 'N' => [1, 3], 'O' => [2, 3], 'P' => [3, 3],
    'Q' => [0, 4], 'R' => [1, 4], 'S' => [2, 4], 'T' => [3, 4],
    'U' => [0, 5], 'V' => [1, 5], 'W' => [2, 5], 'X' => [3, 5],
    'Y' => [0, 6]
];

function astar($graph, $heuristic, $start, $goal, $nodeCoordinates) {
    $frontier = new SplPriorityQueue();
    $frontier->insert($start, 0);
    $cameFrom = [];
    $costSoFar = [$start => 0];

    while (!$frontier->isEmpty()) {
        $currentNode = $frontier->extract();

        if ($currentNode == $goal) {
            $path = [];
            while ($currentNode != $start) {
                $path[] = $currentNode;
                $currentNode = $cameFrom[$currentNode];
            }
            $path[] = $start;
            return array_reverse($path);
        }

        foreach ($graph[$currentNode] as $nextNode => $cost) {
            $newCost = $costSoFar[$currentNode] + $cost;
            if (!isset($costSoFar[$nextNode]) || $newCost < $costSoFar[$nextNode]) {
                $costSoFar[$nextNode] = $newCost;
                $priority = $newCost + $heuristic[$nextNode];
                $frontier->insert($nextNode, -$priority);
                $cameFrom[$nextNode] = $currentNode;
            }
        }
    }

    return null;
}

function calculateDxDy($currentNode, $nextNode, $nodeCoordinates) {
    $currentCoord = $nodeCoordinates[$currentNode];
    $nextCoord = $nodeCoordinates[$nextNode];

    $dx = $nextCoord[0] - $currentCoord[0];
    $dy = $nextCoord[1] - $currentCoord[1];

    return [$dx, $dy];
}

// Process user input and perform A* search
if (isset($_POST['initialNode'])) {
    $initialNode = $_POST['initialNode'];
    $goalNode = 'I'; // Example goal node
    $path = astar($graph, $heuristic, $initialNode, $goalNode, $nodeCoordinates);

    $directions = [];
    for ($i = 0; $i < count($path) - 1; $i++) {
        $currentNode = $path[$i];
        $nextNode = $path[$i + 1];
        [$dx, $dy] = calculateDxDy($currentNode, $nextNode, $nodeCoordinates);

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

    // Output directions
    foreach ($directions as $index => $direction) {
        echo "Step " . ($index + 1) . ": $direction\n";
    }
}
?>