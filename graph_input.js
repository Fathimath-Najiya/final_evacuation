function generateFields() {
    var numberOfNodes = document.getElementById('numberOfNodes').value;
    var nodeFields = document.getElementById('nodeFields');
    nodeFields.innerHTML = '';

    if (numberOfNodes > 0) {
        // Add the paragraph dynamically
        var para = document.createElement('p');
        para.textContent = 'If there is no connection between nodes, then add a zero (0) value.';
        instructionParagraph.appendChild(para);
   
    for (var i = 1; i <= numberOfNodes; i++) {
        var nodeDiv = document.createElement('div');
        nodeDiv.classList.add('node-container');

        // Node Name
        var nameLabel = document.createElement('label');
        nameLabel.textContent = 'Node ' + i + ' Name:';
        var nameInput = document.createElement('input');
        nameInput.type = 'text';
        nameInput.name = 'node_name' + i;
        nameInput.required = true;
        nodeDiv.appendChild(nameLabel);
        nodeDiv.appendChild(nameInput);

        // Coordinate X
        var coordinateLabelX = document.createElement('label');
        coordinateLabelX.textContent = 'Node ' + i + ' Coordinate X:';
        var coordinateInputX = document.createElement('input');
        coordinateInputX.type = 'text';
        coordinateInputX.name = 'coordinate_x' + i;
        coordinateInputX.required = true;
        nodeDiv.appendChild(coordinateLabelX);
        nodeDiv.appendChild(coordinateInputX);

        // Coordinate Y
        var coordinateLabelY = document.createElement('label');
        coordinateLabelY.textContent = 'Node ' + i + ' Coordinate Y:';
        var coordinateInputY = document.createElement('input');
        coordinateInputY.type = 'text';
        coordinateInputY.name = 'coordinate_y' + i;
        coordinateInputY.required = true;
        nodeDiv.appendChild(coordinateLabelY);
        nodeDiv.appendChild(coordinateInputY);

        // Heuristic Value
        var heuristicLabel = document.createElement('label');
        heuristicLabel.textContent = 'Heuristic Value for Node ' + i + ':';
        var heuristicInput = document.createElement('input');
        heuristicInput.type = 'number';
        heuristicInput.name = 'heuristic_value' + i;
        heuristicInput.required = true;
        nodeDiv.appendChild(heuristicLabel);
        nodeDiv.appendChild(heuristicInput);

        var goalNodeLabel = document.createElement('label');
                goalNodeLabel.textContent = 'Goal Node for Node ' + i + ':';
                var goalNodeInput = document.createElement('input');
                goalNodeInput.type = 'text';
                goalNodeInput.name = 'goal_node' + i;
                goalNodeInput.required = true;
                nodeDiv.appendChild(goalNodeLabel);
                nodeDiv.appendChild(goalNodeInput);
        // Distance Inputs (between nodes)
        for (var j = 1; j <= numberOfNodes; j++) {
            if (j !== i) {
                var distanceLabel = document.createElement('label');
                distanceLabel.textContent = 'Distance from Node ' + i + ' to Node ' + j + ':';
                var distanceInput = document.createElement('input');
                distanceInput.type = 'number';
                distanceInput.name = 'distanceFromNode_' + i + '_ToNode_' + j;
                distanceInput.required = true;
                nodeDiv.appendChild(distanceLabel);
                nodeDiv.appendChild(distanceInput);
            }
        }

        nodeFields.appendChild(nodeDiv);
    }
    }
}

// Add event listener to form submission
var graphInputForm = document.getElementById('graphInputForm');
graphInputForm.addEventListener('submit', function(event) {
    var numberOfNodes = document.getElementById('numberOfNodes').value;
    for (var j = 1; j <= numberOfNodes; j++) {
        var node_name = document.getElementsByName('node_name' + j)[0].value;
        var coordinate_x = document.getElementsByName('coordinate_x' + j)[0].value;
        var coordinate_y = document.getElementsByName('coordinate_y' + j)[0].value;
        var heuristic_value = document.getElementsByName('heuristic_value' + j)[0].value;

        // Additional logic for handling distances (if needed)
        var distances = []; // Initialize an array to store distancesfor (var k = 1; k <= numberOfNodes; k++) {
            if (k !== j) {
                var distanceValue = parseFloat(document.getElementsByName('distanceFromNode_' + j + '_ToNode_' + k)[0].value);
                distances.push(distanceValue); // Store the distance value
            }
        }

        // Process the data or send it to the server here
      // Assuming that 'distances' is an array of distance values which needs to be properly defined in your script
console.log(`Node ${j} - Name: ${node_name}, X: ${coordinate_x}, Y: ${coordinate_y}, Heuristic: ${heuristic_value}`);
console.log(`Goal Node for Node ${j}: ${document.getElementsByName('goal_node' + j)[0].value}`);
console.log(`Distances from Node ${j}: ${distances.join(', ')}`);

});