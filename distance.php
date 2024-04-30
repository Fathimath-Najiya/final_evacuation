<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Node Distances</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        select, input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Enter Node Distances</h2>
    <form id="distanceForm" action="save_distances.php" method="post">
        <div id="distanceInputs"></div>

        <button type="submit">Submit Distances</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const numberOfNodes = <?php echo isset($_SESSION['numberOfNodes']) ? $_SESSION['numberOfNodes'] : 0; ?>;
            const container = document.getElementById('distanceInputs');

            if (numberOfNodes > 1) {
                const nodeNames = <?php echo json_encode($_SESSION['nodeNames'] ?? []); ?>;

                for (let i = 0; i < numberOfNodes; i++) {
                    for (let j = i + 1; j < numberOfNodes; j++) {
                        const fromNode = nodeNames[i];
                        const toNode = nodeNames[j];

                        const label = document.createElement('label');
                        label.textContent = Distance from Node ${fromNode} to Node ${toNode}:;

                        const select = document.createElement('select');
                        select.name = distance_${fromNode}_to_${toNode};
                        select.required = true;

                        // Create options for selecting node names
                        nodeNames.forEach(node => {
                            const option = document.createElement('option');
                            option.value = node;
                            option.textContent = node;
                            select.appendChild(option);
                        });

                        container.appendChild(label);
                        container.appendChild(select);
                        container.appendChild(document.createElement('br'));
                    }
                }
            } else {
                const message = document.createElement('p');
                message.textContent = "Not enough nodes available to create distance pairs.";
                container.appendChild(message);
            }
        });
    </script>
</body>
</html>