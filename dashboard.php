<?php
// Include database connection
include 'db_connection.php';

// Set header for JSON output
header("Content-Type: application/json");

// Get the filter parameter from the query string
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Base query
$query = "SELECT id, title, firstname, lastname, email, company, type FROM contacts";
$params = [];
$whereClause = "";

// Add conditions based on the filter
if ($filter === 'sales_leads') {
    $whereClause = " WHERE type = ?";
    $params[] = "Sales Lead";
} elseif ($filter === 'support') {
    $whereClause = " WHERE type = ?";
    $params[] = "Support";
} elseif ($filter === 'assigned_to_me') {
    // Example user ID for demonstration purposes
    $whereClause = " WHERE assigned_to = ?";
    $params[] = 1; // Replace with the actual user ID
}

// Append the WHERE clause to the query
$query .= $whereClause;

try {
    $stmt = $conn->prepare($query);

    // Bind parameters if there are any
    if (!empty($params)) {
        $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $contacts = [];

    // Loop through results
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }

    // Output contacts as JSON
    echo json_encode($contacts);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch contacts."]);
}

$conn->close();
?>
