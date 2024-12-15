document.addEventListener("DOMContentLoaded", function () {
    const userTableBody = document.querySelector(".table tbody");

    // Fetch users via AJAX
    fetch("user_list.php")
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            // Populate table with user data
            userTableBody.innerHTML = "";
            data.forEach((user) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${user.title} ${user.firstname} ${user.lastname}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>${user.created_at}</td>
                `;
                userTableBody.appendChild(row);
            });
        })
        .catch((error) => {
            console.error("Failed to load users:", error);
            userTableBody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; color: red;">Failed to load users. Please try again.</td>
                </tr>
            `;
        });
});
