<?php
include('../../config/connection.php');

// Inisialisasi variabel pencarian
$query = '';
if (isset($_POST['query'])) {
    $query = $_POST['query'];
}

// SQL query
$sql = "SELECT * FROM article";
if (!empty($query)) {
    $sql .= " WHERE title LIKE '%$query%' OR description LIKE '%$query%' OR author LIKE '%$query%'";
}

// Execute query
$result = $conn->query($sql);

// Tombol "btn-back" ditekan
if ($_POST && isset($_POST['btn-back'])) {
    session_destroy();
    header("Location: /pages/login/blogspot.php");
    exit(); // Pastikan untuk keluar setelah mengarahkan ke halaman lain
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/blogspot.css?v=1.4">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>BLOGSPOT</title>
</head>
<body>
    <div>
        <form action="login.php">
            <button type="submit" name="login" value="true">LOGIN</button>
        </form>
    </div>
    <div class="container">
        <h1>BLOGSPOT</h1>
        <!-- Form untuk pencarian -->
        <form action="" method="POST">
            <div class="search-container">
                <input name="query" type="text" placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>">
                <!-- <i class="bi bi-search search-icon"></i> -->
            </div>
        </form>

        <table id="articles-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Image</th>
                    <th style="width: 90px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are results
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '
                            <tr>
                            <td>' . (strlen($row['title']) > 50 ? substr($row['title'], 0, 50) . '...' : $row['title']) . '</td>
                            <td>' . (strlen($row['description']) > 200 ? substr($row['description'], 0, 200) . '...' : $row['description']) . '</td>
                                <td>' . $row['author'] . '</td>
                                <td><img src="' . $row['image'] . '" style="width: 100px;"></td>
                                <td class="view-button">
                                    <a href="../home/form_view.php?login=0&id=' . $row['id'] . '" id="button-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>       
                            </tr>
                        ';
                    }
                } else {
                    echo "
                        <tr>
                            <td colspan='6' style='text-align: center'>Data not found.</td>
                        </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="scripts.js"></script>
</body>

</html>
