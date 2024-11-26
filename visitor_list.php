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

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$search_query = "";
$date_from = "";
$date_to = "";
$sql = "";

// Check if the form is submitted with a date filter or name search
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['filter_by_date']) && !empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $date_from = $_POST['date_from'];
        // Extend the date_to by one day to include today
        $date_to = date('Y-m-d', strtotime($_POST['date_to'] . ' +1 day'));
        
        // Query to get visitors within the date range
        $sql = "SELECT * FROM visitors WHERE check_in_time BETWEEN '$date_from' AND '$date_to'";
    } elseif (isset($_POST['search_by_name']) && !empty($_POST['search_query'])) {
        $search_query = $_POST['search_query'];
        
        // Query to search by full name
        $sql = "SELECT * FROM visitors WHERE full_name LIKE '%$search_query%'";
    }
} 

// Default query for visitors in the last 24 hours if no filter is applied
if (empty($sql)) {
    $sql = "SELECT * FROM visitors WHERE check_in_time >= NOW() - INTERVAL 1 DAY";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor List</title>
    <link rel="stylesheet" href="css/visitor_list.css">
    <style>
        .date {
            padding:1 px;
            justify-content: center;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Visitor List</h1>
    
    <!-- Date Range Filter and Search Form -->
     <div class="date">
    <form method="POST" action="">
        <label for="date_from">Date From:</label>
        <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from); ?>">
        
        <label for="date_to">Date To:</label>
        <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to); ?>">
        
        <button type="submit" name="filter_by_date">Filter by Date</button>
    </form>
    <hr>
    <form method="POST" action="">
        <label for="search_query">Search by Full Name:</label>
        <input type="text" id="search_query" name="search_query" placeholder="Enter full name" value="<?= htmlspecialchars($search_query); ?>">
        
        <button type="submit" name="search_by_name">Search by Name</button>
    </form>
    </div>
    
    <table>
        <tr>
            <th scope="col">Image</th>
            <th scope="col">ID Number/type</th>
            <th scope="col">Full Name</th>
            <th scope="col">Check-In Time</th>
            <th scope="col">Check-Out Time</th>
            <th scope="col">Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td>
                <?php 
                    $visitor_full_name = $row['full_name'];
                    $image_query = "SELECT visitor_image FROM add_image WHERE full_name = ?";
                    $stmt = mysqli_prepare($conn, $image_query);
                    mysqli_stmt_bind_param($stmt, "s", $visitor_full_name);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    
                    $visitor_image = '';
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        mysqli_stmt_bind_result($stmt, $visitor_image);
                        mysqli_stmt_fetch($stmt);
                    }
                    if ($visitor_image) {
                        echo "<img src='" . htmlspecialchars($visitor_image) . "' width='100px' alt='Visitor Image'>";
                    } else {
                        echo "No Image Found";
                    }
                    mysqli_stmt_close($stmt);
                ?>
            </td>
            <td><?= $row['id_number']; ?></td>
            <td><?= $row['full_name']; ?></td>
            <td><?= $row['check_in_time']; ?></td>
            <td><?= $row['check_out_time'] ? $row['check_out_time'] : 'Not Checked Out'; ?></td>
            <td>
                <a href="view_visitor.php?id=<?= $row['id']; ?>"><button class="btn view">View</button></a> 
                <a href="edit_visitor.php?id=<?= $row['id']; ?>"><button class="btn edit">Edit</button></a> 
                <a href="delete_visitor.php?id=<?= $row['id']; ?>"><button class="btn delete">Delete</button></a> 
                <a href="checkout_visitor.php?id=<?= $row['id']; ?>"><button class="btn check_out">Check Out</button></a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>

<?php mysqli_close($conn); ?>
