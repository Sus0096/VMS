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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors Management System Times Global</title>
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
button {
    background-color: #7f1619;
    color: #fff;
    width: 103%;
    padding: 10px;
    border: none;
    cursor: pointer;
}
button:hover {
    background-color: #EE2433;
}

input[type="password"], textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
}
    </style>
</head>
<body>
        <h1> User registration</h1>
    <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST["submit"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    $passwordHash = password_hash($password, PASSWORD_DEFAULT); 
    $errors = array ();

    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        array_push($errors, "All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $passwordRepeat) {
        array_push($errors, "Passwords do not match");
    }
    require_once "db.php"; //connects to database.
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if($rowCount>0) {
        array_push($errors, "Email already exists");
    }
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $passwordHash);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>You are registered successfully.</div>"; // Fixed quotes
        } else {
            die("Something went wrong");
        }
    }
}
?>
    <form action="registration.php" method="post">
        <div class="form-group">
            <p>Username</p>
            <input type="text" class="form-control" name="username" placeholder="username">
        </div>
        <div class="form-group">
        <p>Email</p>

            <input type="email" class="form-control" name="email" placeholder="email">
        </div>
        <div class="form-group">
        <p> Password</p>

            <input type="password" class="form-control" name="password" placeholder="password">
        </div>      
        <div class="form-group">
            <p>Repeat password</p>
            <input type="password" class="form-control" name="repeat_password" placeholder="Repeat password">
        </div>
        <div class="form-btn">
        <button type="submit" name="submit">Submit</button>
        </div>
    </form>
</body>
</html>