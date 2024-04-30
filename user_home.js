// Function to handle adding a building
function addBuilding(buildingId) {
    // Send AJAX request to add building to user's account
    fetch('add_building.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ building_id: buildingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Building added successfully');
            // Reload the page or update UI as needed
        } else {
            alert('Error adding building: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding building');
    });
}

// Event listener for the "Notification" button
// Event listener for the "Notification" button
document.querySelector('.notification-button').addEventListener('click', function() {
   
    alert('Notification button clicked'); // Redirect to another PHP page
    window.location.href = 'notification.php';


  
    
});