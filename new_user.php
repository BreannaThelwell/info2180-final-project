<?php
include 'db_connection.php';
session_start();

// Restrict access to Admins only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Admins only.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email address.']);
        exit();
    }

    // Validate password strength
    if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z]).{8,}$/', $password)) {
        echo json_encode(['error' => 'Password must contain at least one number, one uppercase, one lowercase letter, and be at least 8 characters long.']);
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to create user.']);
    }
}
?>
