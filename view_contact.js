document.addEventListener('DOMContentLoaded', function () {
    const notesContainer = document.getElementById('notes-container');
    const addNoteForm = document.getElementById('add-note-form');

    // Load existing notes (example code for fetching from server)
    fetch('view_contact.php?getNotes=true')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                data.forEach(note => addNoteToUI(note));
            }
        })
        .catch(error => console.error('Error loading notes:', error));

    // Add note form submission
    addNoteForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const noteContent = addNoteForm.querySelector('textarea').value.trim();
        if (noteContent === '') {
            alert('Note cannot be empty');
            return;
        }

        // Add the note to the server (example POST request)
        fetch('view_contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'addNote', content: noteContent }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the note to the UI
                    addNoteToUI({
                        content: noteContent,
                        firstname: 'You', // Replace with the user's name if available
                        lastname: '',
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
});

