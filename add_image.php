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

// Handle image upload
if (isset($_POST['submit'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $id_type = mysqli_real_escape_string($conn, $_POST['id_type']);
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $file_name = basename($file['name']);
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = mime_content_type($file_tmp_name);
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($file_type, $allowed_types)) {
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if ($file_size <= $max_size) {
                $sanitized_file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);

                $target_dir = "uploads/";
                $target_file = $target_dir . uniqid('img_', true) . '-' . $sanitized_file_name;
                
                if (move_uploaded_file($file_tmp_name, $target_file)) {
                    $query = $conn->prepare("INSERT INTO add_image (full_name, visitor_image, id_type) VALUES (?, ?, ?)");
                    $query->bind_param('sss', $full_name, $target_file, $id_type);
                    
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
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo "Error uploading the file. Error code: " . $_FILES['file']['error'];
        }
    }
}

// Handle the search functionality
$search_results = [];
if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search_query']);
    $search_query_sql = "SELECT * FROM add_image WHERE full_name LIKE ?";
    $stmt = $conn->prepare($search_query_sql);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
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

// Handle editing of a specific record
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $conn->prepare("SELECT * FROM add_image WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $document = $result->fetch_assoc();
    $query->close();
    
    // When update form is submitted
    if (isset($_POST['update'])) {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $id_type = mysqli_real_escape_string($conn, $_POST['id_type']);
        
        // Check if new file was uploaded
        $file_path = $document['visitor_image'];
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // Delete old image file
            unlink($file_path);
            
            $file = $_FILES['file'];
            $file_name = basename($file['name']);
            $file_tmp_name = $file['tmp_name'];
            $file_size = $file['size'];
            $file_type = mime_content_type($file_tmp_name);
            
            // Validate file type and size
            if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif']) && $file_size <= 5 * 1024 * 1024) {
                $sanitized_file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);
                $target_file = "uploads/" . uniqid('img_', true) . '-' . $sanitized_file_name;
                move_uploaded_file($file_tmp_name, $target_file);
                
                // Update record with new image
                $query = $conn->prepare("UPDATE add_image SET full_name = ?, visitor_image = ?, id_type = ? WHERE id = ?");
                $query->bind_param('sssi', $full_name, $target_file, $id_type, $id);
            } else {
                echo "Invalid file upload.";
                exit();
            }
        } else {
            // Update only text fields if no new file is uploaded
            $query = $conn->prepare("UPDATE add_image SET full_name = ?, id_type = ? WHERE id = ?");
            $query->bind_param('ssi', $full_name, $id_type, $id);
        }
        
        if ($query->execute()) {
            echo "Record updated successfully!";
        } else {
            echo "Error updating record: " . $query->error;
        }
        $query->close();
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = $conn->prepare("SELECT visitor_image FROM add_image WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $document = $result->fetch_assoc();
    $query->close();
    
    // Delete the file
    unlink($document['visitor_image']);
    
    $delete_query = $conn->prepare("DELETE FROM add_image WHERE id = ?");
    $delete_query->bind_param('i', $id);
    if ($delete_query->execute()) {
        echo "Record deleted successfully!";
    } else {
        echo "Error deleting the record: " . $delete_query->error;
    }
    $delete_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Image and Search</title>
    <link rel="stylesheet" href="css/add_image.css">       
</head>
<body>
    <h1>Add Image</h1>
    <?php if (isset($_GET['id'])): ?>
        <!-- Edit Form for updating record -->
        <form action="add_image.php?id=<?php echo $_GET['id']; ?>" method="post" enctype="multipart/form-data">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($document['full_name']); ?>" required>

            <label for="file">Upload Image:</label>
            <input type="file" id="file" name="file" accept="image/*">

            <label for="id_type">ID Type:</label>
            <select name="id_type" id="id_type" required>
                <option value="visitor" <?php echo ($document['id_type'] == 'visitor') ? 'selected' : ''; ?>>Visitor</option>
                <option value="staff" <?php echo ($document['id_type'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
            </select>

            <input type="submit" name="update" value="Update Image">
        </form>
    <?php else: ?>
        <form action="add_image.php" method="post" enctype="multipart/form-data">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>
            
            <label for="file">Upload Image:</label>
            <input type="file" id="file" name="file" accept="image/*" required>

            <label for="id_type">ID Type:</label>
            <select name="id_type" id="id_type" required>
                <option value="visitor">Visitor</option>
                <option value="staff">Staff</option>
            </select>
            
            <input type="submit" name="submit" value="Upload Image">
        </form>
    <?php endif; ?>

    <hr>

    <h2>Search by Full Name</h2>
    <form action="add_image.php" method="post">
        <input type="text" name="search_query" placeholder="Enter full name" required>
        <input type="submit" name="search" value="Search">
    </form>

    <h3>Stored Available Data</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Visitor Image</th>
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
                        <img src="<?php echo htmlspecialchars($result['visitor_image']); ?>" alt="Visitor Image" width="100">
                    </td>
                    <td><?php echo htmlspecialchars($result['id_type']); ?></td>
                    <td>
                        <a href="add_image.php?id=<?php echo $result['id']; ?>"><button class="edit">Edit</button></a> |
                        <a href="add_image.php?delete_id=<?php echo $result['id']; ?>"><button class="delete">Delete</button></a>
                    </td>
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
