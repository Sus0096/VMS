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

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle image upload and edit logic
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
                $target_dir = "uploadsdocs/";
                $target_file = $target_dir . uniqid('doc_', true) . '-' . $sanitized_file_name;
                
                // Make sure the target directory exists, if not create it
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                // Move the uploaded file to the target directory
                if (move_uploaded_file($file_tmp_name, $target_file)) {
                    // Prepare and execute SQL query to insert data into the 'add_document' table
                    $query = $conn->prepare("INSERT INTO add_document (full_name, document_image, id_type) VALUES (?, ?, ?)");
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
    $search_query_sql = "SELECT * FROM add_document WHERE full_name LIKE ?";
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
    $all_data_query = "SELECT * FROM add_document";
    $result = $conn->query($all_data_query);
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle the edit functionality
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the record from the database
    $query = $conn->prepare("SELECT * FROM add_document WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $document = $result->fetch_assoc();
    
    if (!$document) {
        echo "No document found for the provided ID.";
        exit;
    }
    
    // Handle form submission for updating the record
    if (isset($_POST['update'])) {
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
            
            if (in_array($file_type, $allowed_types)) {
                $max_size = 5 * 1024 * 1024; // 5MB
                if ($file_size <= $max_size) {
                    $sanitized_file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);
                    $target_dir = "uploadsdocs/";
                    $target_file = $target_dir . uniqid('doc_', true) . '-' . $sanitized_file_name;
                    
                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($file_tmp_name, $target_file)) {
                        // Delete the old document file
                        if (file_exists($document['document_image'])) {
                            unlink($document['document_image']); // Delete the old file
                        }

                        $query = $conn->prepare("UPDATE add_document SET full_name = ?, document_image = ?, id_type = ? WHERE id = ?");
                        $query->bind_param('sssi', $full_name, $target_file, $id_type, $id);
                        
                        if ($query->execute()) {
                            echo "Document updated successfully!";
                        } else {
                            echo "Error updating the document.";
                        }
                    } else {
                        echo "Error uploading the file.";
                    }
                } else {
                    echo "File size exceeds the maximum limit.";
                }
            } else {
                echo "Invalid file type.";
            }
        } else {
            // Update without changing the file
            $query = $conn->prepare("UPDATE add_document SET full_name = ?, id_type = ? WHERE id = ?");
            $query->bind_param('ssi', $full_name, $id_type, $id);
            
            if ($query->execute()) {
                echo "Document updated successfully!";
            } else {
                echo "Error updating the document.";
            }
        }
    }
}

// Handle the delete functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Fetch the document image path to delete the file from the server
    $query = $conn->prepare("SELECT document_image FROM add_document WHERE id = ?");
    $query->bind_param('i', $delete_id);
    $query->execute();
    $result = $query->get_result();
    $document = $result->fetch_assoc();
    
    if ($document) {
        $file_path = $document['document_image'];
        
        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path); // Delete the file
        }
        
        // Delete the record from the database
        $query = $conn->prepare("DELETE FROM add_document WHERE id = ?");
        $query->bind_param('i', $delete_id);
        
        if ($query->execute()) {
            echo "Document deleted successfully!";
        } else {
            echo "Error deleting the document.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Document and Search</title>
    <link rel="stylesheet" href="css/doc.css">
    <style>
         
    </style>
</head>
<body>
<h1>Add Verification Document</h1>
<form action="add_document.php" method="post" enctype="multipart/form-data">
    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name" required>

    <label for="file">Upload Verification Document:</label>
    <input type="file" id="file" name="file" accept="image/*" required>

    <label for="id_type">ID Type:</label>
    <select name="id_type" id="id_type" required>
        <option value="Government ID">Government ID</option>
        <option value="Office ID">Office ID</option>
        <option value="Other ID">Other ID</option>
    </select>

    <input type="submit" name="submit" value="Upload Document">
</form>

<hr>

<h2>Search by Full Name</h2>
<form action="add_document.php" method="post">
    <input type="text" name="search_query" placeholder="Enter full name" required>
    <input type="submit" name="search" value="Search">
</form>

<h3>Stored Available Data</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Document Image</th>
            <th>ID Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($search_results as $result): ?>
            <tr>
                <td><?php echo htmlspecialchars($result['id']); ?></td>
                <td><?php echo htmlspecialchars($result['full_name']); ?></td>
                <td>
                    <img src="<?php echo htmlspecialchars($result['document_image']); ?>" alt="Document Image" width="100">
                </td>
                <td><?php echo htmlspecialchars($result['id_type']); ?></td>
                <td>
                    <!-- Edit button -->
                    <a href="add_document.php?id=<?php echo $result['id']; ?>"><button class="edit">Edit</button></a>
                    <!-- Delete button -->
                    <a href="add_document.php?delete_id=<?php echo $result['id']; ?>" onclick="return confirm('Are you sure you want to delete this document?');"><button class="delete">Delete</button></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (isset($document)): ?>
    <!-- Edit Form -->
     <div class="container">
    <h4>Edit Document</h4>
    <form action="add_document.php?id=<?php echo $document['id']; ?>" method="post" enctype="multipart/form-data">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($document['full_name']); ?>" required>

        <label for="file">Upload Verification Document:</label>
        <input type="file" id="file" name="file" accept="image/*">

        <label for="id_type">ID Type:</label>
        <select name="id_type" id="id_type" required>
            <option value="Government ID" <?php if ($document['id_type'] == 'Government ID') echo 'selected'; ?>>Government ID</option>
            <option value="Office ID" <?php if ($document['id_type'] == 'Office ID') echo 'selected'; ?>>Office ID</option>
            <option value="Other ID" <?php if ($document['id_type'] == 'Other ID') echo 'selected'; ?>>Other ID</option>
        </select>

        <input type="submit" name="update" value="Update Document">
    </form>
<?php endif; ?>
</div>
</body>
<footer>
---
© 2024 Awwwdrsh(Aadarsha K.C.). All Rights Reserved.

Open Source, Free for Everyone

</footer>
</html>
