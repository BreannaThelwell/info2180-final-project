<?php
// Include database connection
include 'db_connection.php';

// Set header for JSON output
header("Content-Type: application/json");

// Query to fetch users
$query = "SELECT title, firstname, lastname, email, role, created_at FROM users";

try {
    $result = $conn->query($query);
    $users = [];

    // Loop through results
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Output users as JSON
    echo json_encode($users);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch users."]);
}

$conn->close();
?>
