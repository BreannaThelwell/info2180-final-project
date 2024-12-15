<?php
// Start session and include database connection
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access. Please log in.']);
    exit;
}

// Validate and sanitize the contact ID
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'No contact ID provided.']);
    exit;
}
$contactId = intval($_GET['id']);

// Fetch contact details
$contactQuery = "
    SELECT c.*, 
           u.firstname AS assigned_firstname, 
           u.lastname AS assigned_lastname, 
           creator.firstname AS created_by_firstname, 
           creator.lastname AS created_by_lastname 
    FROM contacts c
    LEFT JOIN users u ON c.assigned_to = u.id
    LEFT JOIN users creator ON c.created_by = creator.id
    WHERE c.id = ?";
$stmt = $conn->prepare($contactQuery);
$stmt->bind_param("i", $contactId);
$stmt->execute();
$contactResult = $stmt->get_result();
if ($contactResult->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Contact not found.']);
    exit;
}
$contact = $contactResult->fetch_assoc();

// Fetch notes for the contact
$notesQuery = "
    SELECT n.*, 
           u.firstname, 
           u.lastname 
    FROM notes n
    LEFT JOIN users u ON n.created_by = u.id 
    WHERE n.contact_id = ? 
    ORDER BY n.created_at DESC";
$notesStmt = $conn->prepare($notesQuery);
$notesStmt->bind_param("i", $contactId);
$notesStmt->execute();
$notesResult = $notesStmt->get_result();

$notes = [];
while ($note = $notesResult->fetch_assoc()) {
    $notes[] = $note;
}

// Handle contact assignment to the logged-in user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_to_me'])) {
        $userId = $_SESSION['user_id'];
        $updateQuery = "UPDATE contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $userId, $contactId);
        $updateStmt->execute();
        echo json_encode(['success' => true, 'message' => 'Contact assigned to you.']);
        exit;
    }

    // Handle switching the contact type
    if (isset($_POST['switch_type'])) {
        $newType = $contact['type'] === 'Sales Lead' ? 'Support' : 'Sales Lead';
        $updateTypeQuery = "UPDATE contacts SET type = ?, updated_at = NOW() WHERE id = ?";
        $updateTypeStmt = $conn->prepare($updateTypeQuery);
        $updateTypeStmt->bind_param("si", $newType, $contactId);
        $updateTypeStmt->execute();
        echo json_encode(['success' => true, 'message' => 'Contact type updated.']);
        exit;
    }

    // Handle adding a new note
    if (isset($_POST['note_content'])) {
        $noteContent = htmlspecialchars(trim($_POST['note_content']));
        $createdBy = $_SESSION['user_id'];
        if (!empty($noteContent)) {
            $addNoteQuery = "INSERT INTO notes (contact_id, content, created_by, created_at) VALUES (?, ?, ?, NOW())";
            $addNoteStmt = $conn->prepare($addNoteQuery);
            $addNoteStmt->bind_param("isi", $contactId, $noteContent, $createdBy);
            $addNoteStmt->execute();

            // Update the contact's timestamp
            $updateContactQuery = "UPDATE contacts SET updated_at = NOW() WHERE id = ?";
            $updateContactStmt = $conn->prepare($updateContactQuery);
            $updateContactStmt->bind_param("i", $contactId);
            $updateContactStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Note added successfully.']);
            exit;
        }
    }
}

// Return contact details and notes as JSON
echo json_encode(['success' => true, 'contact' => $contact, 'notes' => $notes]);
?>
