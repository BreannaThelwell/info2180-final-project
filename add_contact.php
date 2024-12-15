<?php
require 'db_connection.php';

header("Content-Type: application/json");

// Validate required fields
$requiredFields = ['title', 'firstname', 'lastname', 'email', 'type', 'assigned_to'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

// Sanitize and assign input values
$title = htmlspecialchars(trim($_POST['title']));
$firstname = htmlspecialchars(trim($_POST['firstname']));
$lastname = htmlspecialchars(trim($_POST['lastname']));
$email = htmlspecialchars(trim($_POST['email']));
$telephone = htmlspecialchars(trim($_POST['telephone'] ?? '')); // Optional field
$company = htmlspecialchars(trim($_POST['company'] ?? '')); // Optional field
$type = htmlspecialchars(trim($_POST['type']));
$assigned_to = intval($_POST['assigned_to']);
$created_by = 1; // Replace with the actual session user ID if implemented

// Validate type
if (!in_array($type, ['Support', 'Sale Leads'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid contact type.']);
    exit;
}

// Validate the assigned user exists
$checkUserQuery = "SELECT id FROM Users WHERE id = ?";
$checkUserStmt = $conn->prepare($checkUserQuery);
$checkUserStmt->bind_param("i", $assigned_to);
$checkUserStmt->execute();
if ($checkUserStmt->get_result()->num_rows === 0) {
    // Assign to a default user if the provided user does not exist
    $assigned_to = 1; // Replace 1 with the ID of a valid default user
}

// Insert the contact into the database
$query = "INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssssssii", $title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $created_by);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Contact added successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add contact.', 'details' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
