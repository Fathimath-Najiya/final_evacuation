<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buildings</title>
    <style>
        /* Basic styling for buttons */
        button {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            background-color: #0074D9;
            color: #FFFFFF;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Hover effect for buttons */
        button:hover {
            background-color: lightblue;
        }

        /* Center the buttons */
        #buildingCheckboxes {
            text-align: center;
        }

        /* Style the alert button */
        #alertButton {
            margin-top: 20px;
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Buildings</h2>
    <div id="buildingCheckboxes">
        <!-- Building checkboxes will be generated here dynamically -->
    </div>
    <div style="text-align: center;">
        <button id="alertButton">Alert</button>
    </div>
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>
    <script>
        // Check if Firebase is already defined
        if (typeof firebase === 'undefined') {
            // Define Firebase configuration only if Firebase is not already defined
            const firebaseConfig = {
                   // Your Firebase configuration object
                   apiKey: "AIzaSyD_RbtmLvS5e-IIb3PA-le3ykM0STYaLPk",
            authDomain: "emergency-evacuation-390e1.firebaseapp.com",
            projectId: "emergency-evacuation-390e1",
            storageBucket: "emergency-evacuation-390e1.appspot.com",
            messagingSenderId: "214860306545",
            appId: "1:214860306545:web:848549d8d4f3568dd7a3d0",
            measurementId: "G-E8BCGK610J"
            };
            // Initialize Firebase only once
            firebase.initializeApp(firebaseConfig);
        }

        // Function to send notification
        function sendNotification(buildingId) {
            const notificationMessage = 'Alert: Emergency situation detected in your building!';
            fetch('notification.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ buildingIds: [buildingId] }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Notification sent:', data);
            })
            .catch(error => console.error('Error sending notification:', error));
        }

        document.getElementById('alertButton').addEventListener('click', function () {
            const selectedBuildingIds = Array.from(document.querySelectorAll('.building-checkbox:checked'))
                .map(checkbox => checkbox.getAttribute('data-building-id'));

            if (selectedBuildingIds.length === 0) {
                alert('Please select at least one building.');
                return;
            }

            selectedBuildingIds.forEach(buildingId => {
                sendNotification(buildingId);
            });
        });
    </script>
</body>
</html>
