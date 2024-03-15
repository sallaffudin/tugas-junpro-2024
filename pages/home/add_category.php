<?php
include('../../config/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan judul dan deskripsi dikirimkan
    if (isset($_POST['add-title']) && isset($_POST['add-description'])) {
        $title = $_POST['add-title'];
        $description = $_POST['add-description'];

        // Persiapkan query untuk menyimpan kategori baru
        $query = "INSERT INTO category (judul, deskripsi) VALUES ('$title', '$description')";

        // Jalankan query
        if (mysqli_query($conn, $query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => 'Error adding category: ' . mysqli_error($conn)));
        }
    } else {
        echo json_encode(array('error' => 'Title and description are required'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request method'));
}
?>
