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

// Fetch data
$total_visitors_query = "SELECT COUNT(*) AS total FROM visitors WHERE DATE(check_in_time) = CURDATE()";
$total_visitors_result = mysqli_query($conn, $total_visitors_query);
if (!$total_visitors_result) {
    die("Database query failed: " . mysqli_error($conn));
}
$total_visitors = mysqli_fetch_assoc($total_visitors_result)['total'];

$unexited_visitors_query = "SELECT COUNT(*) AS unexited FROM visitors WHERE DATE(check_in_time) = CURDATE() AND check_out_time IS NULL";
$unexited_visitors_result = mysqli_query($conn, $unexited_visitors_query);
if (!$unexited_visitors_result) {
    die("Database query failed: " . mysqli_error($conn));
}
$unexited_visitors = mysqli_fetch_assoc($unexited_visitors_result)['unexited'];

$exited_visitors_query = "SELECT COUNT(*) AS exited FROM visitors WHERE DATE(check_in_time) = CURDATE() AND check_out_time IS NOT NULL";
$exited_visitors_result = mysqli_query($conn, $exited_visitors_query);
if (!$exited_visitors_result) {
    die("Database query failed: " . mysqli_error($conn));
}
$exited_visitors = mysqli_fetch_assoc($exited_visitors_result)['exited'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Management System - Dashboard</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <h1>Welcome to  Visitor Management System !!!</h1>
    <div class="dashboard">
        <div class="dashboard-item1">
            <h2>Total Visitors Today</h2>
            <p><?php echo htmlspecialchars($total_visitors); ?></p>
        </div>
        <div class="dashboard-item2">
            <h2>Unexited Visitors</h2>
            <p><?php echo htmlspecialchars($unexited_visitors); ?></p>
        </div>
        <div class="dashboard-item">
            <h2>Exited Visitors</h2>
            <p><?php echo htmlspecialchars($exited_visitors); ?></p>
        </div>
    </div>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
