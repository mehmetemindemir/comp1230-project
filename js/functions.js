function validateLoginForm() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;

    // Check if username is at least 3 characters long and contains no special characters
    const usernameRegex = /^[a-zA-Z0-9]{3,}$/;
    if (!usernameRegex.test(username)) {
        alert('Username must be at least 3 characters long and contain no special characters.');
        return false;
    }

    // Check if password is at least 9 characters long
    if (password.length < 9) {
        alert('Password must be at least 9 characters long.');
        return false;
    }

    return true; // Allow form submission
}