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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM visitors WHERE id=$id");
    $visitor = mysqli_fetch_assoc($result);
}

if (isset($_POST['submit'])) {
    $id_number = $_POST['id_number'];
    $full_name = $_POST['full_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $reason = $_POST['reason'];
    $approved_by = $_POST['approved_by'];
    $requested_by = $_POST['requested_by'];
    $request_source = $_POST['request_source'];

    // Update the visitor record in the database
    $query = "UPDATE visitors SET id_number='$id_number', full_name='$full_name', contact='$contact', email='$email', reason='$reason', approved_by='$approved_by', requested_by='$requested_by', request_source='$request_source' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: visitor_list.php"); // Redirect after update
        exit();
    } else {
        echo "Error updating visitor: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Visitor Details</title>
    <style>
            body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

h1 {
    background-color: #7f1619;
    color: #fff;
    padding: 10px;
    text-align: center;
}
for
h1 {
    position: absolute;
    top: 10px; /* Distance from the top of the container */
    left: 20%; /* Center horizontally */
}
form {
    max-width: 700px;
    margin: auto;
    padding: 50px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
label {
    display: block;
    margin: 10px 0 5px;
}
input[type="text"], input[type="email"], textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
}
input[type="submit"], button {
    background-color: #7f1619;
    color: #fff;
    width: 103%;
    padding: 10px;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover, button:hover {
    background-color: #EE2433;
}

        
    </style>
</head>
<body>
    <h1>Edit Visitor Details</h1>
    <form action="" method="post">
        <label for="id_number">ID Number/type:</label><br>
        <input type="text" id="id_number" name="id_number" value="<?php echo htmlspecialchars($visitor['id_number']); ?>"><br>

        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($visitor['full_name']); ?>"><br>

        <label for="contact">Contact:</label><br>
        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($visitor['contact']); ?>"><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($visitor['email']); ?>"><br>

        <label for="reason">Reason:</label><br>
        <input type="text" id="reason" name="reason" value="<?php echo htmlspecialchars($visitor['reason']); ?>"><br>

        <label for="approved_by">Approved By:</label><br>
        <input type="text" id="approved_by" name="approved_by" value="<?php echo htmlspecialchars($visitor['approved_by']); ?>"><br>

        <label for="requested_by">Requested By:</label><br>
        <input type="text" id="requested_by" name="requested_by" value="<?php echo htmlspecialchars($visitor['requested_by']); ?>"><br>

        <label for="request_source">Request Source:</label><br>
        <input type="text" id="request_source" name="request_source" value="<?php echo htmlspecialchars($visitor['request_source']); ?>"><br>

        <input type="submit" name="submit" value="Update">
    </form>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
