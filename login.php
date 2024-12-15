<?php
session_start();
header('Content-Type: application/json');
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
        exit;
    }

    // Query the database
    $stmt = $conn->prepare('SELECT id, email, password, role FROM Users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            echo json_encode(['success' => true, 'redirect' => 'dashboard.html']);
            exit;
        }
    }

    echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
exit;
?>
