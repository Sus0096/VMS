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
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM visitors WHERE id=$id");
    header('Location: visitor_list.php');
}
?>
