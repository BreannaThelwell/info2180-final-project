document.addEventListener("DOMContentLoaded", function () {
    const userTableBody = document.querySelector(".table tbody");

    // Function to fetch and display users
    function loadUsers() {
        fetch("user_list.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderUsers(data.users);
                } else {
                    userTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" style="color: red; text-align: center;">Failed to load users</td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching users:", error);
                userTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" style="color: red; text-align: center;">Error loading users</td>
                    </tr>
                `;
            });
    }

    // Function to render users into the table
    function renderUsers(users) {
        userTableBody.innerHTML = ""; // Clear existing rows

        users.forEach(user => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${user.firstname} ${user.lastname}</td>
                <td>${user.email}</td>
                <td>${user.role}</td>
                <td>${user.created_at}</td>
            `;
            userTableBody.appendChild(row);
        });
    }

    // Load users on page load
    loadUsers();
});
