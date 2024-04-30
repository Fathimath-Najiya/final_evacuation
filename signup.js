function signup() {
    var ownername = document.getElementById('ownername').value.trim();
    var password = document.getElementById('password').value.trim();
    var confirmPassword = document.getElementById('confirmPassword').value.trim();
    var errorMessage = document.getElementById('errorMessage');
    window.location.href = 'login.html';
    // Client-side validation
    if (!ownername || !password || !confirmPassword) {
        errorMessage.textContent = 'Please enter all fields.';
        return;
    }

    if (password !== confirmPassword) {
        errorMessage.textContent = 'Passwords do not match.';
        return;
    }

    fetch('connect.php', {
        method: 'POST',
        body: JSON.stringify({ ownername: ownername, password: password }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Signup successful!');
            // Redirect to login.html
            window.location.href = 'login.html'; // Assuming login.html is in the same directory
        } else {
            console.error('Signup failed:', data.message); // Log detailed error message
            errorMessage.textContent = data.message; // Display error message to the user
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorMessage.textContent = 'Error occurred. Please try again.';
    });
}