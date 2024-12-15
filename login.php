<?php
//Start session
session_start();

header('Content-Type: application/json');//Send response as JSON

// Database connection
require 'db_connection.php'; 

//Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user inputs
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
        exit;
    }

    // Query to fetch the user based on email
    $stmt = $conn->prepare('SELECT email, password, role FROM Users WHERE email = ?');
    $stmt->bind_param('s', $email); //Bind email parameter
    $stmt->execute();
    $result = $stmt->get_result();
    
    //Check if user with provided email exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            // Redirect to dashboard
            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
            exit();
        } else {
            // Password is incorrect
            echo json_encode(['success' => false, 'error' => 'Invalid email or password!']);
            exit;
        }
    } else {
        // User not found
        echo json_encode(['success' => false, 'error' => 'Invalid email or password!']);
        exit;
    }

    $stmt->close();
} else {
    // Reject non-POST requests
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}
?>
