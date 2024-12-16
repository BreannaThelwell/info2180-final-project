document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("add-contact-form");
    const assignedToSelect = document.getElementById("assigned_to");
    const responseMessage = document.getElementById("response-message");

    // Load only admin users into the "Assign To" dropdown
    function loadAdmins() {
        fetch("add_contact.php?action=getAdmins")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    assignedToSelect.innerHTML = '<option value="">Select Admin</option>'; // Clear and set placeholder
                    data.admins.forEach(admin => {
                        const option = document.createElement("option");
                        option.value = admin.id;
                        option.textContent = `${admin.firstname} ${admin.lastname}`;
                        assignedToSelect.appendChild(option);
                    });
                } else {
                    assignedToSelect.innerHTML = `<option value="">Error loading admins</option>`;
                    console.error("Error fetching admins:", data.error);
                }
            })
            .catch(error => {
                assignedToSelect.innerHTML = `<option value="">Error loading admins</option>`;
                console.error("Error:", error);
            });
    }

    // Handle form submission
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch("add_contact.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    responseMessage.textContent = "Contact added successfully!";
                    responseMessage.style.color = "green";
                    form.reset();
                } else {
                    responseMessage.textContent = `Error: ${data.error}`;
                    responseMessage.style.color = "red";
                }
            })
            .catch(error => {
                responseMessage.textContent = "An unexpected error occurred.";
                responseMessage.style.color = "red";
                console.error("Error submitting form:", error);
            });
    });

    // Load admins on page load
    loadAdmins();
});
