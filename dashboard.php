<?php
// Start session to maintain login state
session_start();

// Database connection file
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get filter from URL or default to "all"
$filter = $_GET['filter'] ?? 'all';
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Build SQL query based on filter type
$query = "SELECT * FROM contacts";
if ($filter === 'sales_leads') {
    $query .= " WHERE type = 'Sales Lead'";
} elseif ($filter === 'support') {
    $query .= " WHERE type = 'Support'";
} elseif ($filter === 'assigned_to_me') {
    $query .= " WHERE assigned_to = $user_id";
}
// Execute the query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM Dashboard</title>
    <style>
        /* Basic styles for the dashboard */
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f6; }
        .sidebar { width: 200px; background: #2C3E50; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; margin: 5px 0; }
        .sidebar a:hover { background: #34495E; }
        .main { margin-left: 220px; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .btn { padding: 10px 20px; background: #6C5CE7; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #4B4CDA; }
        .table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table th { background: #f4f4f6; }
        .badge { padding: 5px 10px; border-radius: 5px; color: white; font-size: 12px; }
        .badge.sales-lead { background: #6C5CE7; }
        .badge.support { background: #2ECC71; }
        .filters a { margin-right: 10px; text-decoration: none; color: #34495E; }
        .filters a.active { font-weight: bold; }
    </style>
</head>
<body>
    
     <!-- Sidebar navigation -->
    <div class="sidebar">
        <h2>Dolphin CRM</h2>
        <a href="dashboard.php">Home</a>
        <a href="add_contact.php">New Contact</a>
        <a href="users.php">Users</a>
        <a href="logout.php">Logout</a>
    </div>
     <!-- Main content area -->
    <div class="main">
        <div class="header">
            <h1>Dashboard</h1>
            <a href="add_contact.php" class="btn">+ Add Contact</a>
        </div>
        <!-- Links to filter the dashboard by contact type -->
        <div class="filters">
            <a href="dashboard.php?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All</a>
            <a href="dashboard.php?filter=sales_leads" class="<?= $filter === 'sales_leads' ? 'active' : '' ?>">Sales Leads</a>
            <a href="dashboard.php?filter=support" class="<?= $filter === 'support' ? 'active' : '' ?>">Support</a>
            <a href="dashboard.php?filter=assigned_to_me" class="<?= $filter === 'assigned_to_me' ? 'active' : '' ?>">Assigned to Me</a>
        </div>

        <table class="table">
           <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
               
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['title'] . ' ' . $row['firstname'] . ' ' . $row['lastname'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['company'] ?></td>
                        <td>
                            <span class="badge <?= strtolower(str_replace(' ', '-', $row['type'])) ?>">
                                <?= strtoupper($row['type']) ?>
                            </span>
                        </td>
                        <td><a href="view_contact.php?id=<?= $row['id'] ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script></script>
</body>
</html>