<?php
require 'db_connection.php';
session_start();
header("Content-Type: application/json");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access. Please log in.']);
    exit;
}

$contactId = intval($_GET['id'] ?? 0);
$loggedInUserId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch contact details and notes
    $contactQuery = "SELECT c.*, u.firstname AS assigned_firstname, u.lastname AS assigned_lastname 
                     FROM contacts c
                     LEFT JOIN users u ON c.assigned_to = u.id
                     WHERE c.id = ?";
    $stmt = $conn->prepare($contactQuery);
    $stmt->bind_param("i", $contactId);
    $stmt->execute();
    $contact = $stmt->get_result()->fetch_assoc();

    $notesQuery = "SELECT n.comment, n.created_at, u.firstname, u.lastname 
                   FROM notes n 
                   LEFT JOIN users u ON n.created_by = u.id 
                   WHERE n.contact_id = ?
                   ORDER BY n.created_at DESC";
    $stmt = $conn->prepare($notesQuery);
    $stmt->bind_param("i", $contactId);
    $stmt->execute();
    $notes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'contact' => $contact, 'notes' => $notes]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';

    if ($action === 'addNote') {
        $noteContent = $data['content'];
        $addNoteQuery = "INSERT INTO notes (contact_id, comment, created_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($addNoteQuery);
        $stmt->bind_param("isi", $contactId, $noteContent, $loggedInUserId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } elseif ($action === 'assignToMe') {
        $assignQuery = "UPDATE contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($assignQuery);
        $stmt->bind_param("ii", $loggedInUserId, $contactId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } elseif ($action === 'switchType') {
        $switchQuery = "UPDATE contacts SET type = CASE WHEN type = 'Support' THEN 'Sales Lead' ELSE 'Support' END, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($switchQuery);
        $stmt->bind_param("i", $contactId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    }
}

$conn->close();
?>
