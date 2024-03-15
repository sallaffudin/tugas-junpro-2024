<?php
include('../../config/connection.php');

// Function to get category data by ID
function getCategoryById($categoryId, $conn) {
    $query = "SELECT * FROM category WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Function to update category data
function updateCategory($categoryId, $title, $description, $conn) {
    $query = "UPDATE category 
              SET judul = ?, deskripsi = ?
              WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $categoryId);
    return mysqli_stmt_execute($stmt);
}

// Check if it's a GET request and 'id' parameter is set
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $categoryId = $_GET['id'];
    $categoryData = getCategoryById($categoryId, $conn);
    
    if ($categoryData) {
        echo json_encode($categoryData);
    } else {
        echo json_encode(array('error' => 'Category not found.'));
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required parameters are set
    if (isset($_POST['edit-category-id'], $_POST['edit-title'], $_POST['edit-description'])) {
        $categoryId = $_POST['edit-category-id'];
        $title = $_POST['edit-title'];
        $description = $_POST['edit-description'];

        // Update category data
        if (updateCategory($categoryId, $title, $description, $conn)) {
            echo json_encode(array('success' => 'Category data updated successfully.'));
        } else {
            echo json_encode(array('error' => 'Failed to update category data.'));
        }
    } else {
        echo json_encode(array('error' => 'Incomplete data provided.'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request.'));
}
?>
