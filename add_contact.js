document.addEventListener("DOMContentLoaded", function () {
    const addContactForm = document.getElementById("add-contact-form");
    const assignedToDropdown = document.getElementById("assigned_to");
    const responseMessage = document.getElementById("response-message");

    // Load available users for "Assigned To" dropdown
    fetch("get_users.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                assignedToDropdown.innerHTML = data.users
                    .map(user => `<option value="${user.id}">${user.firstname} ${user.lastname}</option>`)
                    .join("");
            } else {
                responseMessage.textContent = "Failed to load users.";
                responseMessage.style.color = "red";
            }
        })
        .catch(error => {
            console.error("Error loading users:", error);
            responseMessage.textContent = "An error occurred while loading users.";
            responseMessage.style.color = "red";
        });

    // Handle form submission
    addContactForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(addContactForm);

        // Send contact data to the server
        fetch("add_contact.php", {
            method: "POST",
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    responseMessage.textContent = "Contact added successfully!";
                    responseMessage.style.color = "green";
                    addContactForm.reset();
                } else {
                    responseMessage.textContent = data.error || "Failed to add contact.";
                    responseMessage.style.color = "red";
                }
            })
            .catch(error => {
                console.error("Error adding contact:", error);
                responseMessage.textContent = "An error occurred while adding the contact.";
                responseMessage.style.color = "red";
            });
    });
});
