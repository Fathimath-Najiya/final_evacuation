document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        
        var formData = new FormData(document.getElementById('loginForm'));

        fetch('logino.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Login successful!');
                window.location.href = 'home.html'; // Redirect to home.html if login is successful
            } else {
                console.error('Login failed:', data.message);
                document.getElementById('errorMessage').textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('errorMessage').textContent = 'Error occurred. Please try again.';
        });
    });
});
