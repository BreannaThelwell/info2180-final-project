<?php
require 'db_connection.php';
header("Content-Type: application/json");

// Fetch admin users for "Assign To" dropdown
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAdmins') {
    try {
        $query = "SELECT id, firstname, lastname FROM users WHERE role = 'admin'";
        $result = $conn->query($query);

        $admins = [];
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }

        echo json_encode(['success' => true, 'admins' => $admins]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Add a new contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $company = $_POST['company'] ?? '';
    $type = $_POST['type'] ?? 'Support';
    $assigned_to = intval($_POST['assigned_to'] ?? 0);

    if (empty($firstname) || empty($lastname) || empty($email) || empty($company) || !$assigned_to) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    try {
        // Validate assigned user
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin'");
        $stmt->bind_param("i", $assigned_to);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Assigned user must be an admin.']);
            exit;
        }

        // Insert contact
        $query = "INSERT INTO contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssi", $title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Contact added successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
