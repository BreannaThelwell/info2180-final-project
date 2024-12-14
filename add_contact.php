<?php
//php file for adding contacts to contacts table

// Start session to maintain login state
session_start();

// Include database connection utility
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form inputs
    $title = htmlspecialchars(trim($_POST['title']));
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $company = htmlspecialchars(trim($_POST['company']));
    $type = htmlspecialchars(trim($_POST['type']));
    $assigned_to = intval($_POST['assigned_to']); // Assigned user's ID
    $created_by = $_SESSION['user_id']; // Creator's user ID

    // Insert new contact into the database
    $stmt = $conn->prepare("INSERT INTO contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssssiii", $title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $created_by);

    // Execute the statement and provide feedback
    if ($stmt->execute()) {
        echo "Contact added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch users for the "Assigned To" dropdown
$users_query = "SELECT id, firstname, lastname FROM users";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contact</title>
    <style>
        /* Basic styling */
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f6; }
        .sidebar { width: 200px; background: #2C3E50; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; margin: 5px 0; }
        .sidebar a:hover { background: #34495E; }
        .main { margin-left: 220px; padding: 20px; }
        .header h1 { margin: 0; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { padding: 10px 20px; background: #6C5CE7; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #4B4CDA; }
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
            <h1>Add New Contact</h1>
        </div>
        <!-- Contact creation form -->
        <form action="add_contact.php" method="POST">
            <!-- Title -->
            <div class="form-group">
                <label for="title">Title</label>
                <select name="title" id="title" required>
                    <option value="Mr.">Mr.</option>
                    <option value="Ms.">Ms.</option>
                    <option value="Mrs.">Mrs.</option>
                    <option value="Dr.">Dr.</option>
                </select>
            </div>
            <!-- First name -->
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname" required>
            </div>
            <!-- Last name -->
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" id="lastname" required>
            </div>
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <!-- Telephone -->
            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="text" name="telephone" id="telephone">
            </div>
            <!-- Company -->
            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" name="company" id="company" required>
            </div>
            <!-- Type -->
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" required>
                    <option value="Sales Lead">Sales Lead</option>
                    <option value="Support">Support</option>
                </select>
            </div>
            <!-- Assigned to -->
            <div class="form-group">
                <label for="assigned_to">Assigned To</label>
                <select name="assigned_to" id="assigned_to" required>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= $user['firstname'] . ' ' . $user['lastname'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <!-- Submit button -->
            <button type="submit" class="btn">Save</button>
        </form>
    </div>
</body>
</html>