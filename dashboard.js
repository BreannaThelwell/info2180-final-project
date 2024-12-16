document.addEventListener("DOMContentLoaded", function () {
    const contactList = document.getElementById("contact-list");
    const filterLinks = document.querySelectorAll(".filter-link");

    // Function to fetch and display contacts
    function loadContacts(filter = "all") {
        fetch(`dashboard.php?filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderContacts(data.contacts);
                } else {
                    contactList.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; color: red;">${data.error || "Failed to load contacts"}</td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching contacts:", error);
                contactList.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; color: red;">Error loading contacts</td>
                    </tr>
                `;
            });
    }

    // Function to render contacts in the table
    function renderContacts(contacts) {
        contactList.innerHTML = ""; // Clear existing rows
        if (data.length === 0) {
            contactList.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: grey;">No contacts found</td>
                </tr>
            `;
            return;
        }

        data.forEach(contact => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${contact.firstname} ${contact.lastname}</td>
                <td>${contact.email}</td>
                <td>${contact.company}</td>
                <td>${contact.type}</td>
                <td><a href="view_contact.html?id=${contact.id}" class="btn">View</a></td>
            `;
            contactList.appendChild(row);
        });
    }

    // Render error message
    function renderError(message) {
        contactList.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; color: red;">${message}</td>
            </tr>
        `;
    }

    // Add event listeners to filter links
    filterLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            // Highlight the active filter
            filterLinks.forEach(link => link.classList.remove("active"));
            this.classList.add("active");

            // Load contacts based on the selected filter
            const filter = this.id.replace("filter-", ""); // Extract the filter type
            loadContacts(filter);
        });
    });

    // Load all contacts on page load
    loadContacts();
});
