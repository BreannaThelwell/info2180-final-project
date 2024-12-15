document.addEventListener("DOMContentLoaded", function () {
    const contactList = document.getElementById("contact-list");
    const filters = document.querySelectorAll(".filter-link");

    // Fetch and render contacts
    function fetchContacts(filter = "all") {
        fetch(`dashboard.php?filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderContacts(data.contacts);
                } else {
                    console.error("Failed to fetch contacts:", data.error);
                    contactList.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; color: red;">Failed to load contacts. Please try again.</td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching contacts:", error);
                contactList.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; color: red;">Failed to connect to the server.</td>
                    </tr>
                `;
            });
    }

    // Render contacts into the table
    function renderContacts(data) {
        contactList.innerHTML = ""; // Clear existing content

        if (data.length === 0) {
            contactList.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: grey;">No contacts found.</td>
                </tr>
            `;
            return;
        }

        data.forEach(contact => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${contact.title} ${contact.firstname} ${contact.lastname}</td>
                <td>${contact.email}</td>
                <td>${contact.company}</td>
                <td>${contact.type}</td>
                <td>
                    <a href="view_contact.html?id=${contact.id}" class="btn">View</a>
                </td>
            `;
            contactList.appendChild(row);
        });
    }

    // Handle filter clicks
    filters.forEach(filter => {
        filter.addEventListener("click", function (e) {
            e.preventDefault();
            filters.forEach(f => f.classList.remove("active"));
            this.classList.add("active");

            const filterType = this.id.replace("filter-", "");
            fetchContacts(filterType);
        });
    });

    // Initial fetch for all contacts
    fetchContacts();
});
