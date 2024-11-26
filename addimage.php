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
<?php
include 'db.php';

// Handle image upload
if (isset($_POST['submit'])) {
    // Sanitize the full name and id_type input
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $id_type = mysqli_real_escape_string($conn, $_POST['id_type']);
    
    // Check if a file is uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $file_name = basename($file['name']);
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = mime_content_type($file_tmp_name);
        
        // Define allowed file types (only image files)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Check if the file is an image
        if (in_array($file_type, $allowed_types)) {
            // Define maximum file size (e.g., 5MB)
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if ($file_size <= $max_size) {
                // Sanitize the file name to avoid special characters
                $sanitized_file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);

                // Define target directory and file path
                $target_dir = "uploads/";
                $target_file = $target_dir . uniqid('img_', true) . '-' . $sanitized_file_name;
                
                // Move the uploaded file to the target directory
                if (move_uploaded_file($file_tmp_name, $target_file)) {
                    // Prepare and execute SQL query to insert data into the 'add_image' table
                    $query = $conn->prepare("INSERT INTO add_image (full_name, visitor_image, id_type) VALUES (?, ?, ?)");
                    $query->bind_param('sss', $full_name, $target_file, $id_type); // 'sss' for string (full_name, file path, id_type)
                    
                    if ($query->execute()) {
                        echo "File uploaded successfully!";
                    } else {
                        echo "Error saving to the database: " . $query->error;
                    }
                    $query->close();
                } else {
                    echo "Error uploading the file.";
                }
            } else {
                echo "File size exceeds the maximum limit of 5MB.";
            }
        } else {
            echo "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
        }
    } else {
        // Check for file upload errors
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo "Error uploading the file. Error code: " . $_FILES['file']['error'];
        }
    }
}

// Handle the search functionality
$search_results = [];

// If search is performed
if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search_query']);
    
    // Query the database for records matching the search query
    $search_query_sql = "SELECT * FROM add_image WHERE full_name LIKE ?";
    $stmt = $conn->prepare($search_query_sql);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the results
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    $stmt->close();
}

// If no search is performed, fetch all data
if (empty($search_results)) {
    $all_data_query = "SELECT * FROM add_image";
    $result = $conn->query($all_data_query);
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Image and Search</title>
    <link rel="stylesheet" href="css/add_image.css">
    <style>

h2 {
    color: #7f1619;
    text-align: center;
    position: absolute;
    top: 410px; 
    left: 37%;
    transform: translateX(-50%);
}
* Absolute positioning */
.table-container {
    position: relative;
}
table {
    position: absolute;
    top: 50px; /* Distance from the top of the container */
    left: 70%; /* Center horizontally */
    transform: translateX(-50%); /* Adjust to center */
}
/* Relative positioning */
table {
    position: relative;
    top: 10px; /* Moves the table down */
    left: 800px; /* Moves the table right */
}
table { 
    width: 80%;
    border-collapse: collapse;
    margin: 20px 0;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}
h3 {
    color: #7f1619;
    text-align: center;
    position: absolute;
    top: 590px; 
    left: 35%;
    transform: translateX(-50%);
}
    </style>
</head>
<body>
    <h1>Add Image</h1>
    <form action="add_image.php" method="post" enctype="multipart/form-data">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>
        
        <label for="file">Upload Image:</label>
        <input type="file" id="file" name="file" accept="image/*" required>

        <!-- Add a dropdown or radio buttons to select the ID type -->
        <label for="id_type">ID Type:</label>
        <select name="id_type" id="id_type" required>
            <option value="visitor">Visitor</option>
            <option value="staff">Staff</option>
        </select>
        
        <input type="submit" name="submit" value="Upload Image">
    </form>

    <hr>

    <!-- Search Form -->
    <h2>Search by Full Name</h2>
    <form action="add_image.php" method="post">
        <input type="text" name="search_query" placeholder="Enter full name" required>
        <input type="submit" name="search" value="Search">
    </form>

    <!-- Display Results in Table -->
    <h3>Stored Available Data</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Visitor Image</th>
                <th>ID Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($search_results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['id']); ?></td>
                    <td><?php echo htmlspecialchars($result['full_name']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($result['visitor_image']); ?>" alt="Visitor Image" width="100">
                    </td>
                    <td><?php echo htmlspecialchars($result['id_type']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
