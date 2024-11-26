<?php
session_start(); // Start the session

// Redirect to login page if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

// Content of the home page for logged-in users
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        .sidebar {
            height: 100%;
            width: 200px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
        
        }
        .sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .sidebar a:hover {
            background-color: #7f1619;
            color: #FFFFFF;
        }
        .container {
            margin-left: 300px; /* Adjusted to leave space for the sidebar */
            padding: 20px;
        }
        .h{
             
    background-color: #333;
    color: #fff;
    padding: 10px;
    text-align: center;

        }
        .logo{
            width: 180px;
            height: 90px;
        }
        .menu{
            margin-top : 50px;
        }
    </style>
</head>
<body>
    <nav>
    <div class="sidebar">
        <img  class="logo" src="pic/timesglobal.png" alt="timeslogo">
        <div class="menu">
        <a href="home.php">Home</a>
        <a href="add_record.php">Add Record</a>
        <a href="visitor_list.php">Visitor List</a>
        <a href="reports.php">Reports</a>
        <a href="add_document.php">Add Document</a>
        <a href="add_image.php">Add Image</a>
        <a href="registration.php">Register User</a>
        <a href="logout.php">Log Out</a>
        </div>
    </div>
    </nav>
</body>
</html>