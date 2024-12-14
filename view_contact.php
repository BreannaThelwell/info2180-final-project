<?php
// Start session and include database connection
session_start();

include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the contact details based on the provided ID
$contact_id = intval($_GET['id']); // Sanitize the ID from the URL
$query = "SELECT c.*, u.firstname as assigned_firstname, u.lastname as assigned_lastname, 
                 creator.firstname as created_by_firstname, creator.lastname as created_by_lastname 
          FROM contacts c
          LEFT JOIN users u ON c.assigned_to = u.id
          LEFT JOIN users creator ON c.created_by = creator.id
          WHERE c.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $contact_id); // Bind the contact ID parameter
$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc(); // Fetch the contact details

// Redirect if the contact is not found
if (!$contact) {
    die("Contact not found.");
}

// Handle assigning the contact to the logged-in user
if (isset($_POST['assign_to_me'])) {
    $user_id = $_SESSION['user_id']; // Get the current user's ID
    $update_query = "UPDATE contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $user_id, $contact_id);
    $update_stmt->execute();
    header("Location: view_contact.php?id=$contact_id"); // Refresh the page
    exit();
}

// Handle switching the contact type
if (isset($_POST['switch_type'])) {
    $new_type = $contact['type'] === 'Sales Lead' ? 'Support' : 'Sales Lead'; // Toggle type
    $update_type_query = "UPDATE contacts SET type = ?, updated_at = NOW() WHERE id = ?";
    $update_type_stmt = $conn->prepare($update_type_query);
    $update_type_stmt->bind_param("si", $new_type, $contact_id);
    $update_type_stmt->execute();
    header("Location: view_contact.php?id=$contact_id"); // Refresh the page
    exit();
}

// Fetch notes for the contact
$notes_query = "SELECT n.*, u.firstname, u.lastname FROM notes n 
                LEFT JOIN users u ON n.created_by = u.id 
                WHERE n.contact_id = ? ORDER BY n.created_at DESC";
$notes_stmt = $conn->prepare($notes_query);
$notes_stmt->bind_param("i", $contact_id);
$notes_stmt->execute();
$notes_result = $notes_stmt->get_result(); // Fetch the notes

// Handle adding a new note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note_content'])) {
    $note_content = htmlspecialchars(trim($_POST['note_content'])); // Sanitize the note content
    $created_by = $_SESSION['user_id']; // Get the current user's ID
    if (!empty($note_content)) {
        // Insert the note into the database
        $add_note_query = "INSERT INTO notes (contact_id, content, created_by, created_at) VALUES (?, ?, ?, NOW())";
        $add_note_stmt = $conn->prepare($add_note_query);
        $add_note_stmt->bind_param("isi", $contact_id, $note_content, $created_by);
        $add_note_stmt->execute();
        // Update the contact's "updated_at" timestamp
        $update_contact_query = "UPDATE contacts SET updated_at = NOW() WHERE id = ?";
        $update_contact_stmt = $conn->prepare($update_contact_query);
        $update_contact_stmt->bind_param("i", $contact_id);
        $update_contact_stmt->execute();
        header("Location: view_contact.php?id=$contact_id"); // Refresh the page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Contact</title>
    <style>
          /* Basic styles for the page */
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f6; }
        .sidebar { width: 200px; background: #2C3E50; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; margin: 5px 0; }
        .sidebar a:hover { background: #34495E; }
        .main { margin-left: 220px; padding: 20px; }
        .header h1 { margin: 0; margin-bottom: 20px; }
        .btn { padding: 10px 20px; background: #6C5CE7; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn.green { background: #2ECC71; }
        .btn:hover { background: #4B4CDA; }
        .contact-details { background: #fff; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .notes-section { background: #fff; padding: 20px; border-radius: 5px; }
        .note { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .note:last-child { border-bottom: none; }
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
         <!-- Header section with the contact's name -->
        <div class="header">
            <!-- Display the contact's full name -->
            <h1><?= $contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname'] ?></h1>
        </div>
          <!-- Contact details section -->
        <div class="contact-details">
            <p><strong>Email:</strong> <?= $contact['email'] ?></p>
            <p><strong>Telephone:</strong> <?= $contact['telephone'] ?></p>
            <p><strong>Company:</strong> <?= $contact['company'] ?></p>
            <p><strong>Assigned To:</strong> <?= $contact['assigned_firstname'] . ' ' . $contact['assigned_lastname'] ?></p> <!-- Name of the person assigned to this contact -->
            <p><strong>Created By:</strong> <?= $contact['created_by_firstname'] . ' ' . $contact['created_by_lastname'] ?> on <?= $contact['created_at'] ?></p><!-- Name of the user who created this contact -->
            <p><strong>Last Updated:</strong> <?= $contact['updated_at'] ?></p><!-- Last update timestamp -->
            <!-- Form to assign the contact to the logged-in user or switch contact type -->
            <form method="POST">
                <button type="submit" name="assign_to_me" class="btn green">Assign to Me</button>
                 <!-- Toggle button to switch between Sales Lead and Support -->
                <button type="submit" name="switch_type" class="btn"><?= $contact['type'] === 'Sales Lead' ? 'Switch to Support' : 'Switch to Sales Lead' ?></button>
            </form>
        </div>
        
        <!-- Notes section -->
        <div class="notes-section">
            <h3>Notes</h3>
            <!-- Loop through and display each note associated with the contact -->
            <?php while ($note = $notes_result->fetch_assoc()): ?>
                <div class="note">
                    <p><strong><?= $note['firstname'] . ' ' . $note['lastname'] ?>:</strong> <?= $note['content'] ?></p> <!-- Display the name of the user who created the note and the note's content  -->
                    <p><small><?= $note['created_at'] ?></small></p><!-- Display the creation timestamp of the note -->
                </div>
            <?php endwhile; ?>
            
            <!-- Form to add a new note -->
            <form method="POST">
                <textarea name="note_content" rows="3" style="width: 100%; padding: 10px; margin-top: 10px;" placeholder="Add a note"></textarea>
                <button type="submit" class="btn" style="margin-top: 10px;">Add Note</button>
            </form>
        </div>
    </div>
</body>
</html>