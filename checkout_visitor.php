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
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the input

    // Check if the visitor has already checked out
    $result = mysqli_query($conn, "SELECT check_out_time FROM visitors WHERE id=$id");
    $visitor = mysqli_fetch_assoc($result);

    if ($visitor) {
        if ($visitor['check_out_time'] === null) {
            // Update check_out_time if not already checked out
            $check_out_time = date('Y-m-d H:i:s');
            mysqli_query($conn, "UPDATE visitors SET check_out_time='$check_out_time' WHERE id=$id");
            header('Location: visitor_list.php?message=Checkout successful');
        } else {
            // Redirect with a message if already checked out
            header('Location: visitor_list.php?message=Visitor already checked out');
        }
    } else {
        echo "Visitor not found.";
    }
    exit();
} else {
    echo "No visitor ID provided.";
}
?>
