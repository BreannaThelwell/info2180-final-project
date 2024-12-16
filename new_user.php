<?php
session_start();
require 'db_connection.php';
header('Content-Type: application/json');

// Debug: Check session values
if (!isset($_SESSION['user_id']) || strpos($_SESSION['role'], 'admin') !== 0) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

// Process POST request to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
    $lastname = htmlspecialchars(trim($_POST['lastname'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = htmlspecialchars(trim($_POST['role'] ?? ''));

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Email already exists.']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("sssss", $firstname, $lastname, $email, $hashedPassword, $role);
        $insertStmt->execute();

        echo json_encode(['success' => true, 'message' => 'User created successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to create user.']);
    }
}
?>
