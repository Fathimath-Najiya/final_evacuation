function showRegisteredBuildings() {
    document.getElementById('registeredBuildingsList').classList.add('hidden');
    // Redirect to the registered_buildings.html page
    window.location.href = 'registered_buildings.php'; // Change 'registered_buildings.html' to the appropriate URL
}

function showBuildingRegistrationForm() {
    // Hide the registered buildings list
    document.getElementById('registeredBuildingsList').classList.add('hidden');
    // If you want to redirect to register.html, uncomment the following line
     window.location.href = 'register.html';
}

// Function to show an alert and redirect to another page
function showAlertAndRedirect() {
    alert('Button clicked!'); // Display an alert message
    // Redirect to another page
    window.location.href = 'alert.php'; // Change 'another_page.html' to the appropriate URL
}

// You can add more JavaScript logic as needed