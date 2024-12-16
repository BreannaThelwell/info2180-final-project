<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

try {
    // Fetch all users from the database
    $query = "SELECT firstname, lastname, email, role, created_at FROM users ORDER BY created_at DESC";
    $result = $conn->query($query);

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to load user data']);
    exit();
}
$conn->close();
?>
