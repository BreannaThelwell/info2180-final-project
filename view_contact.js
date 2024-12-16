document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const contactId = params.get("id");
    const notesContainer = document.getElementById("notes-container");
    const addNoteForm = document.getElementById("add-note-form");

    if (!contactId) {
        alert("Invalid contact ID.");
        window.location.href = "dashboard.html";
        return;
    }

    // Fetch and populate contact details and notes
    function fetchContactDetails() {
        fetch(`view_contact.php?id=${contactId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contact = data.contact;
                    const notes = data.notes;

                    // Populate contact details
                    document.getElementById("contact-name").textContent = `${contact.title} ${contact.firstname} ${contact.lastname}`;
                    document.getElementById("email").textContent = contact.email;
                    document.getElementById("telephone").textContent = contact.telephone || "N/A";
                    document.getElementById("company").textContent = contact.company || "N/A";
                    document.getElementById("assigned-to").textContent = contact.assigned_to
                        ? `${contact.assigned_firstname} ${contact.assigned_lastname}`
                        : "Unassigned";
                    document.getElementById("created-info").textContent = `Created: ${contact.created_at}`;
                    document.getElementById("updated-info").textContent = `Updated: ${contact.updated_at}`;

                    // Render notes
                    notesContainer.innerHTML = ""; // Clear old notes
                    notes.forEach(note => {
                        const noteElement = document.createElement("div");
                        noteElement.classList.add("note");
                        noteElement.innerHTML = `
                            <p><strong>${note.firstname} ${note.lastname}:</strong> ${note.comment}</p>
                            <p class="note-date">${note.created_at}</p>
                        `;
                        notesContainer.appendChild(noteElement);
                    });
                } else {
                    alert("Failed to load contact details.");
                    window.location.href = "dashboard.html";
                }
            })
            .catch(error => {
                console.error("Error loading contact details:", error);
                alert("An error occurred while loading the contact.");
            });
    }

    // Add Note
    addNoteForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const noteContent = addNoteForm.querySelector("textarea").value.trim();

        if (!noteContent) {
            alert("Note content cannot be empty.");
            return;
        }

        fetch("view_contact.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "addNote", content: noteContent, contactId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the new note to the UI
                    const noteElement = document.createElement("div");
                    noteElement.classList.add("note");
                    noteElement.innerHTML = `
                        <p><strong>You:</strong> ${noteContent}</p>
                        <p class="note-date">${new Date().toLocaleString()}</p>
                    `;
                    notesContainer.prepend(noteElement);

                    // Clear the textarea
                    addNoteForm.reset();
                } else {
                    alert("Failed to add note. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error adding note:", error);
                alert("An error occurred while adding the note.");
            });
    });

    // Assign to Me
    document.getElementById("assign-to-me-btn").addEventListener("click", function () {
        fetch("view_contact.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "assignToMe", contactId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Contact assigned to you successfully.");
                    fetchContactDetails(); // Refresh the contact details
                } else {
                    alert("Failed to assign contact. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error assigning contact:", error);
                alert("An error occurred. Please try again.");
            });
    });

    // Switch Type
    document.getElementById("switch-type-btn").addEventListener("click", function () {
        fetch("view_contact.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "switchType", contactId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Contact type switched successfully.");
                    fetchContactDetails(); // Refresh the contact details
                } else {
                    alert("Failed to switch contact type. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error switching contact type:", error);
                alert("An error occurred. Please try again.");
            });
    });

    // Initial fetch
    fetchContactDetails();
});
