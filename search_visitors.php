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
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php'; // Ensure this includes the correct path to your db.php

$searchTerm = '';
$results = [];

// Check if the form is submitted
if (isset($_POST['search'])) {
    $searchTerm = trim($_POST['searchTerm']);
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM visitors WHERE full_name LIKE :searchTerm OR contact LIKE :searchTerm");
    $stmt->execute(['searchTerm' => "%$searchTerm%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Visitors</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to your CSS -->
</head>
<body>
    <h1>Search Visitors</h1>
    <form action="search_visitors.php" method="post">
        <input type="text" name="searchTerm" placeholder="Enter full name or contact" value="<?php echo htmlspecialchars($searchTerm); ?>" required>
        <input type="submit" name="search" value="Search">
    </form>

    <?php if (isset($_POST['search'])): ?>
        <h2>Search Results:</h2>
        <?php if (count($results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $visitor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($visitor['id']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['contact']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No results found for "<?php echo htmlspecialchars($searchTerm); ?>".</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
