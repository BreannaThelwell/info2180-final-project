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
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
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
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error preparing contact query.']);
    exit;
} //Error handling

$stmt->bind_param("i", $contactId);
$stmt->execute();
$contactResult = $stmt->get_result();
if ($contactResult->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Contact not found.']);
    exit;
}
$contact = $contactResult->fetch_assoc();

// Check if only notes are requested
if (isset($_GET['getNotes']) && $_GET['getNotes'] === 'true') {
    fetchNotes($conn, $contactId);
    exit;
}

// Fetch notes for the contact
$notes = fetchNotes($conn, $contactId, false);

// Handle POST requests for assignments, type switching, or adding notes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = json_decode(file_get_contents('php://input'), true);

    // Assign contact to logged-in user
    if (isset($postData['assign_to_me'])) {
        assignContactToUser($conn, $contactId, $_SESSION['user_id']);
        exit;
    } 

    
    // Switch contact type
    if (isset($postData['switch_type'])) {
        switchContactType($conn, $contactId, $contact['type']);
        exit;
    } 

    // Add a new note
    if (isset($postData['note_content'])) {
        addContactNote(
            $conn,
            $contactId,
            $_SESSION['user_id'],
            htmlspecialchars(trim($postData['note_content']))
        );
        exit;
    }
} 

// Return contact details and notes as JSON
echo json_encode(['success' => true, 'contact' => $contact, 'notes' => $notes]);
exit;

// Fetch notes for the contact
function fetchNotes($conn, $contactId, $output = true) {
     $notesQuery = "
        SELECT n.*, 
               u.firstname, 
               u.lastname 
        FROM Notes n
        LEFT JOIN Users u ON n.created_by = u.id 
        WHERE n.contact_id = ? 
        ORDER BY n.created_at DESC";
    $notesStmt = $conn->prepare($notesQuery);
    if (!$notesStmt) {
        $error = ['success' => false, 'error' => 'Error preparing notes query.'];
        if ($output) echo json_encode($error);
        return $error;
    }
    $notesStmt->bind_param("i", $contactId);
    $notesStmt->execute();
    $notesResult = $notesStmt->get_result();

    $notes = [];
    while ($note = $notesResult->fetch_assoc()) {
        $notes[] = $note;
    }
    if ($output) {
        echo json_encode(['success' => true, 'notes' => $notes]);
    } else {
        return $notes;
    }
}

// Handle contact assignment to the logged-in user
//Modular attempt
function assignContactToUser($conn, $contactId, $userId) {
    $updateQuery = "UPDATE Contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error preparing assignment query.']);
        return;
    }
    $stmt->bind_param("ii", $userId, $contactId);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Contact assigned to you.']);
}

    // Handle switching the contact type
function switchContactType($conn, $contactId, $currentType) {
    $newType = $currentType === 'Sales Leads' ? 'Support' : 'Sales Leads';
    $updateTypeQuery = "UPDATE Contacts SET type = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateTypeQuery);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error preparing type switch query.']);
        return;
    }
    $stmt->bind_param("si", $newType, $contactId);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Contact type updated.']);
}

    // Handle adding a new note
    function addContactNote($conn, $contactId, $createdBy, $noteContent) {
    if (empty($noteContent)) {
        echo json_encode(['success' => false, 'error' => 'Note content cannot be empty.']);
        return;
    }
    $addNoteQuery = "
        INSERT INTO Notes (contact_id, content, created_by, created_at) 
        VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($addNoteQuery);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error preparing note insertion query.']);
        return;
    }
    $stmt->bind_param("isi", $contactId, $noteContent, $createdBy);
    $stmt->execute();

            // Update the contact's timestamp
           $updateContactQuery = "UPDATE Contacts SET updated_at = NOW() WHERE id = ?";
    $updateStmt = $conn->prepare($updateContactQuery);
    $updateStmt->bind_param("i", $contactId);
    $updateStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Note added successfully.']);
}

/* Return contact details and notes as JSON
echo json_encode(['success' => true, 'contact' => $contact, 'notes' => $notes]); */
?>
