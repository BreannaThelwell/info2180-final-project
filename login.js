document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', async function (e) {
        e.preventDefault(); // Stop the form from submitting traditionally

        const formData = new FormData(form);

        try {
            // Send login data to login.php
            const response = await fetch('login.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                // Redirect to dashboard.html
                window.location.href = result.redirect;
            } else {
                // Display error message
                document.getElementById('error-message').textContent = result.error;
            }
        } catch (error) {
            console.error('Login Error:', error);
            document.getElementById('error-message').textContent = 'An unexpected error occurred.';
        }
    });
});
