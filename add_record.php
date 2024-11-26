<?php
session_start(); // Start the session

// Redirect to login page if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

// Content of the home page for logged-in users
?>

<?php
include 'sidebar.php'; 
include 'db.php';

// Handle new visitor check-in
if (isset($_POST['submit'])) {
    // Sanitize and validate inputs
    $id_number = trim($_POST['id_number']);
    $full_name = trim($_POST['full_name']);
    $contact = trim($_POST['contact']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $reason = trim($_POST['reason']);
    $approved_by = trim($_POST['approved_by']);
    $requested_by = trim($_POST['requested_by']);
    $request_source = trim($_POST['request_source']);

    if (!$email) {
        echo "Invalid email format.";
        exit;
    }

    // Get current timestamp for check-in time
    $check_in_time = date('Y-m-d H:i:s');

    // Prepare SQL statement
    $stmt = mysqli_prepare($conn, "INSERT INTO visitors (id_number, full_name, contact, email, reason, approved_by, requested_by, request_source, check_in_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssssssss", $id_number, $full_name, $contact, $email, $reason, $approved_by, $requested_by, $request_source, $check_in_time);

    // Execute and check for errors
    if (mysqli_stmt_execute($stmt)) {
        echo "New visitor record created successfully.";
        // Optionally redirect or clear the form
        // header("Location: success_page.php");
        // exit();
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Visitor Record</title>
    <link rel="stylesheet" href="css/add_record.css">
</head>
<body>
    <h1>Add New Visitor</h1>
    <form action="add_record.php" method="post">
        <label for="id_number">ID Number/type:</label>
        <input type="text" id="id_number" name="id_number" required>
        
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>
        
        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason" required></textarea>
        
        <label for="approved_by">Approved By:</label>
        <input type="text" id="approved_by" name="approved_by" required>
        
        <label for="requested_by">Requested By:</label>
        <input type="text" id="requested_by" name="requested_by" required>
        
        <label for="request_source">Request Source:</label>
        <input type="text" id="request_source" name="request_source" required>
        
        <input type="submit" name="submit" value="Check In">
    </form>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
