<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', function () {
    const notesContainer = document.getElementById('notes-container');
    const addNoteForm = document.getElementById('add-note-form');
    const contactId = getContactId(); 


    // Load existing notes (example code for fetching from server)
    function loadNotes() {
         fetch(`view_contact.php?id=${contactId}&getNotes=true`)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.notes)) {
                    notesContainer.innerHTML = ''; // Clear existing notes
                    data.notes.forEach(note => addNoteToUI(note));
                } else {
                    notesContainer.innerHTML = '<p>No notes found for this contact.</p>';
                }
            })
            .catch(error => {
                console.error('Error loading notes:', error);
                notesContainer.innerHTML = '<p>Error loading notes. Please try again later.</p>';
            });
    }//added feedback message for display

    // Fetch and load notes on page load
    loadNotes();
=======
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
>>>>>>> fa100bd (Fixed Add User and View Contact)

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
        
        // Disable the submit button during the request
        const submitButton = addNoteForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;

<<<<<<< HEAD
        // Add the note to the server (example POST request)
        fetch('view_contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'addNote', content: noteContent, contactId: contactId }),
=======
        fetch("view_contact.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "addNote", content: noteContent, contactId }),
>>>>>>> fa100bd (Fixed Add User and View Contact)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
<<<<<<< HEAD
                    // Add the note to the UI
                    addNoteToUI({
                        content: noteContent,
                       firstname: data.currentUser.firstname, // Dynamically use the user's name from server response
                        lastname: data.currentUser.lastname,
                        created_at: new Date().toLocaleString(),
                    });
=======
                    // Add the new note to the UI
                    const noteElement = document.createElement("div");
                    noteElement.classList.add("note");
                    noteElement.innerHTML = `
                        <p><strong>You:</strong> ${noteContent}</p>
                        <p class="note-date">${new Date().toLocaleString()}</p>
                    `;
                    notesContainer.prepend(noteElement);
>>>>>>> fa100bd (Fixed Add User and View Contact)

                    // Clear the textarea
                    addNoteForm.reset();
                } else {
                    alert("Failed to add note. Please try again.");
                }
            })
            .catch(error => {
<<<<<<< HEAD
                console.error('Error adding note:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                submitButton.disabled = false; // Re-enable the submit button
            });
    });

    // Function to add a note to the UI
    function addNoteToUI(note) {
        const noteElement = document.createElement('div');
        noteElement.classList.add('note');
        noteElement.innerHTML = `
            <p><strong>${note.firstname} ${note.lastname}:</strong></p>
            <p>${note.content}</p>
            <p class="note-date">${note.created_at}</p>
        `;
        notesContainer.insertBefore(noteElement, notesContainer.firstChild);
    }
    
 // Helper function to get the contactId 
    function getContactId() {
        //Extract contactId from URL query params
        const params = new URLSearchParams(window.location.search);
        return params.get('id');
    }
});
=======
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
>>>>>>> fa100bd (Fixed Add User and View Contact)

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
