document.getElementById('buildingRegistrationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
    
    var formData = new FormData(this);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Building registered successfully');
            // Redirect to graph_input.html after successful registration
            window.location.href = 'graph_input.html';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error registering building');
    });
});
