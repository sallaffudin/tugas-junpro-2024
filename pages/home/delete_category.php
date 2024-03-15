<?php
include('../../config/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $categoryId = $_POST['id'];

    if (!$conn) {
        echo json_encode(array('error' => 'Database connection failed: ' . mysqli_connect_error()));
        exit;
    }

    $query = "DELETE FROM category WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('error' => 'Categories that have been mapped cannot be deleted'));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(array('error' => 'Invalid request'));
}
?>
