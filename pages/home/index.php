<?php
session_start();
include('../../config/connection.php');

if (count($_SESSION) < 1) {
    header("Location: /pages/login");
    exit();
}

$user = $_SESSION['user'];
$is_admin = isset($user['is_admin']) ? $user['is_admin'] : 0;

// Terima parameter simpan dari POST
$simpan = isset($_GET['simpan']) ? $_GET['simpan'] : '';

// Tampilkan notifikasi jika parameter simpan bernilai 1
if ($simpan == 1) {
    echo "<script>alert('Artikel berhasil disimpan');</script>";
}

$query = '';
if (isset($_POST['query'])) {
    $query = $_POST['query'];
}

if ($_GET && isset($_GET['id'])) {
    // SQL query to delete data
    $sql = "DELETE FROM article WHERE id='" . $_GET['id'] . "'"; // Change to your table name

    if ($conn->query($sql) === TRUE) {
        header("Location: /pages/home/index.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if ($_POST && isset($_POST['logout'])) {
    session_destroy();
    header("Location: /pages/login/blogspot.php");
    exit(); // Add exit() to terminate the script after redirecting
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/index.css?v=1.4">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Manage Articles</title>
</head>

<body>
    <div>
        <div class="logout-container">
            <div class="dropdown" id="profileDropdown">
                <button class="dropbtn"><i class="bi bi-person-circle"></i> <?php echo $user['name']; ?> <i class="bi bi-caret-down-fill"></i></button>
                <div class="dropdown-content">
                    <button class="edit-profile-button" onclick="location.href='profile.php'"><i class="bi bi-person"></i> Edit Profile</button>
                    <div class="dropdown-divider"></div>
                    <form action="" method="post">
                        <button class="logout-button" name="logout" value="true"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>Manage Articles</h1>
        <a href="form_add.php" style="text-decoration: none">
            <button id="add-article-btn">+ Add New Article</button>
        </a>
        <form action="" method="POST">
            <div class="search-container">
                <input name="query" type="text" placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>">
                <!-- <i class="bi bi-search search-icon"></i> -->
            </div>
        </form>
        <table id="articles-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th style="width: 153px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SQL query
                $sql = "SELECT * FROM article"; // Change to your table name

                // If the user is not an admin, only show articles owned by the user
                if ($is_admin == 1) {
                    $sql .= " WHERE author = '" . $user['name'] . "'"; // Change 'author' to the appropriate column name in your article table
                }

                // If search query exists, add WHERE clause to SQL query
                if (!empty($query)) {
                    $sql .= " WHERE title LIKE '%" . $query . "%'";
                }

                // Execute query
                $result = $conn->query($sql);

                // Check if there are results
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '
                            <tr>
                                <td>' . $row['id'] . '</td>
                                <td>' . (strlen($row['title']) > 50 ? substr($row['title'], 0, 50) . '...' : $row['title']) . '</td>
                                <td>' . (strlen($row['description']) > 200 ? substr($row['description'], 0, 200) . '...' : $row['description']) . '</td>
                                <td>' . $row['author'] . '</td>
                                <td><img src="' . $row['image'] . '" style="width: 100px;"></td>
                                <td>' . $row['created_at'] . '</td>
                                <td>' . $row['updated_at'] . '</td>
                                <td>
                                    <a href="form_view.php?login=1&id=' . $row['id'] . '" id="button-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="form_edit.php?id=' . $row['id'] . '" id="button-edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="?id=' . $row['id'] . '" id="button-hapus" onclick="return confirmDelete()">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        ';

                        // You can access other columns similarly
                    }
                } else {
                    echo "
                        <tr>
                            <td colspan='8' style='text-align: center'>Data not found.</td>
                        </tr>
                        ";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        // JavaScript for dropdown functionality
        document.getElementById("profileDropdown").addEventListener("click", function() {
            var dropdownContent = this.getElementsByClassName("dropdown-content")[0];
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });

        // JavaScript function to confirm delete action
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus artikel ini?");
        }
    </script>
</body>

</html>
