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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Visitor</title>
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
.container {
    padding: 1px;
    background-color: #e8f7f8;
    text-align: left;

}
    </style>
</head>
<body>
<h1>Visitor Details</h1>
<div class="container">
    <?php if ($visitor) { ?>
        <p><strong>ID Number/type:</strong> <?php echo $visitor['id_number']; ?></p>
        <p><strong>Full Name:</strong> <?php echo $visitor['full_name']; ?></p>
        <p><strong>Contact:</strong> <?php echo $visitor['contact']; ?></p>
        <p><strong>Email:</strong> <?php echo $visitor['email']; ?></p>
        <p><strong>Reason:</strong> <?php echo $visitor['reason']; ?></p>
        <p><strong>Approved By:</strong> <?php echo $visitor['approved_by']; ?></p>
        <p><strong>Requested By:</strong> <?php echo $visitor['requested_by']; ?></p>
        <p><strong>Request Source:</strong> <?php echo $visitor['request_source']; ?></p>
        <p><strong>Check-In Time:</strong> <?php echo $visitor['check_in_time']; ?></p>
        <p><strong>Check-Out Time:</strong> <?php echo $visitor['check_out_time'] ? $visitor['check_out_time'] : 'Not Checked Out'; ?></p>

        <p><strong>Image:</strong></p>
        <?php 
            // Get the full name of the visitor
            $visitor_full_name = $visitor['full_name'];

            // Query to get the image from the 'add_image' table based on full name
            $image_query = "SELECT visitor_image FROM add_image WHERE full_name = ?";
            $stmt = mysqli_prepare($conn, $image_query);
            mysqli_stmt_bind_param($stmt, "s", $visitor_full_name);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            // Declare a variable to hold the image file name
            $visitor_image = '';

            // Check if any row is returned from the second query
            if (mysqli_stmt_num_rows($stmt) > 0) {
                // Bind the result and fetch the image
                mysqli_stmt_bind_result($stmt, $visitor_image);
                mysqli_stmt_fetch($stmt);
            }

            // Check if image exists and display it
            if ($visitor_image) {
                // Display image from the correct path
                echo "<img src='" . htmlspecialchars($visitor_image) . "' width='500px' alt='Visitor Image'>";
            } else {
                // If no image is found, show this message
                echo "No Image Found";
            }

            // Close the prepared statement
            mysqli_stmt_close($stmt);
        ?>
    <?php } else { ?>
        <p>No visitor found with this ID.</p>
    <?php } ?>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
