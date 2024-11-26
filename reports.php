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

// Variable to store the error message (if any)
$error_message = "";

// Check if the form is submitted and the start and end dates are set
if (isset($_POST['submit'])) {
    // Get the start and end date values from the form
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Debug: Check if dates are received
    if (empty($start_date) || empty($end_date)) {
        $error_message = "Start date and End date are required.";
    } else {
        // Prepare the SQL query to fetch visitors within the date range
        // Using prepared statements to prevent SQL injection
        $query = "SELECT * FROM visitors WHERE check_in_time BETWEEN ? AND ?";
        $stmt = mysqli_prepare($conn, $query);

        // Check if the query was prepared successfully
        if ($stmt === false) {
            $error_message = "Error preparing query: " . mysqli_error($conn);
            echo $error_message;  // Debugging
        } else {
            // Bind parameters and execute the query
            $start_date_time = $start_date . " 00:00:00";
            $end_date_time = $end_date . " 23:59:59";
            mysqli_stmt_bind_param($stmt, "ss", $start_date_time, $end_date_time);

            // Execute the query
            if (!mysqli_stmt_execute($stmt)) {
                $error_message = "Error executing query: " . mysqli_stmt_error($stmt);
                echo $error_message;  // Debugging
            } else {
                // Store the result
                $result = mysqli_stmt_get_result($stmt);
            }

            // Close the prepared statement
            mysqli_stmt_close($stmt);
        }
    }
} else {
    // Fetch all visitors if no date filter is applied
    $query = "SELECT * FROM visitors";
    $result = mysqli_query($conn, $query);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Reports</title>
    <link rel="stylesheet" href="css/report.css">
</head>
<body>
    <h1>Visitor Reports</h1>

    <!-- Display any error messages -->
    <?php if ($error_message): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Form to filter reports by date range -->
    <div class="report-filter">
        <form action="" method="POST">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" required>
            <input type="submit" name="submit" value="Generate Report">
        </form>
    </div>

    <!-- Displaying the report table -->
    <?php if (isset($result) && mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>ID Number/type</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Reason</th>
                    <th>Approved By</th>
                    <th>Requested By</th>
                    <th>Request Source</th>
                    <th>Check-in Date and Time</th>
                    <th>Check-out Date and Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($visitor = mysqli_fetch_assoc($result)): ?>
                    <tr>
                    <td>
    <?php 
    // Get the full name of the visitor
    $visitor_full_name = $visitor['full_name']; // Use the correct variable name

    // Query to get the image from the second table (add_image) based on full name
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
        echo "<img src='" . htmlspecialchars($visitor_image) . "' width='100px' alt='Visitor Image'>";
    } else {
        // If no image is found, show this message
        echo "No Image Found";
    }

    mysqli_stmt_close($stmt);
    ?>
</td>

                        <td><?php echo htmlspecialchars($visitor['id_number']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['contact']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['email']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['reason']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['approved_by']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['requested_by']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['request_source']); ?></td>
                        <td>
                            <?php
                            // Format the check-in datetime
                            $check_in = new DateTime($visitor['check_in_time']);
                            echo $check_in->format('Y-m-d H:i:s');
                            ?>
                        </td>
                        <td>
                            <?php
                            // Format the check-out datetime
                            $check_out = new DateTime($visitor['check_out_time']);
                            echo $check_out->format('Y-m-d H:i:s');
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No visitors found for the selected date range.</p>
    <?php endif; ?>

    <!-- Option to Print the Report -->
    <button onclick="window.print()" class="print">Print Report</button>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
