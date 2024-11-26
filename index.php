<?php
session_start(); // Start the session to track login state

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php"; // Ensure this connects to your database

// Check if the user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    header("Location: home.php"); // Redirect to home if already logged in
    exit();
}

// Handle login when the form is submitted
if (isset($_POST["login"])) {
    $email = trim($_POST["email"]); // Trim any whitespace
    $password = $_POST["password"];
    
    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
            if ($user) {
                // Check if the password matches
                if (password_verify($password, $user["password"])) {
                    // Set session variables upon successful login
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user["id"]; // Store user ID or other relevant info
                    $_SESSION['username'] = $user["email"]; // You can store email or name as username
                    
                    // Redirect to home.php after successful login
                    header("Location: home.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Password is incorrect.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Email does not exist.</div>";
            }
        } else {
            echo "Query execution failed: " . mysqli_error($conn);
        }
    } else {
        echo "<div class='alert alert-danger'>Database query failed.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors Management System</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
   <div class="container">
        <div class="login-box">
            <h2>Login</h2>
            <form action="index.php" method="POST">
                <div class="input-box">
                    <input type="email" name="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="forgot-pass">
                    <a href="#">Forgot your password?</a>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
                <div class="signup-link">
                    <a href="registration.php">Signup</a>
                </div>
            </form>
        </div>
        <div class="animation-wrapper">
            <?php for ($i = 0; $i < 50; $i++): ?>
                <span style="--i:<?= $i; ?>"></span>
            <?php endfor; ?>
        </div>
    </div>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
