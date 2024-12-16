<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

// Get the filter parameter from the query string
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Base query
$query = "SELECT c.id, c.firstname, c.lastname, c.email, c.company, c.type, c.assigned_to, 
                 u.firstname AS assigned_to_name 
          FROM contacts c
          LEFT JOIN users u ON c.assigned_to = u.id";
$whereClause = "";
$params = [];

// Singular filter logic
if ($filter === 'sales_leads') {
    $whereClause = " WHERE c.type = ?";
    $params[] = "Sales Lead";
} elseif ($filter === 'support') {
    $whereClause = " WHERE c.type = ?";
    $params[] = "Support";
} elseif ($filter === 'assigned') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
        exit;
    }
    $whereClause = " WHERE c.assigned_to = ?";
    $params[] = $_SESSION['user_id'];
}

// Combine query with WHERE clause for filtering
$query .= $whereClause;

try {
    $stmt = $conn->prepare($query);

    // Bind parameters if any
    if (!empty($params)) {
        $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }

    echo json_encode(['success' => true, 'contacts' => $contacts]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch contacts.']);
    exit();
}
$conn->close();
?>
