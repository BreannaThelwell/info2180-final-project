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

    // Add note form submission
    addNoteForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const noteContent = addNoteForm.querySelector('textarea').value.trim();
        if (noteContent === '') {
            alert('Note cannot be empty');
            return;
        }
        
        // Disable the submit button during the request
        const submitButton = addNoteForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        // Add the note to the server (example POST request)
        fetch('view_contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'addNote', content: noteContent, contactId: contactId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the note to the UI
                    addNoteToUI({
                        content: noteContent,
                       firstname: data.currentUser.firstname, // Dynamically use the user's name from server response
                        lastname: data.currentUser.lastname,
                        created_at: new Date().toLocaleString(),
                    });

                    // Clear the textarea
                    addNoteForm.querySelector('textarea').value = '';
                } else {
                    alert('Failed to add note. Please try again.');
                }
            })
            .catch(error => {
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

