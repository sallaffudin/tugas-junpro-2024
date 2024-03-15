<?php
session_start();
include('../../config/connection.php');

if (count($_SESSION) < 1) {
    header("Location: /pages/login");
    exit();
}

$user = $_SESSION['user'];
$is_admin = isset($user['is_admin']) ? $user['is_admin'] : 0;

$items_per_page = 3;

$simpan = isset($_GET['simpan']) ? $_GET['simpan'] : '';

if ($simpan == 1) {
    echo "<script>alert('Artikel berhasil disimpan');</script>";
}

$query = '';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
}

$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

$sql = "SELECT article.*, category.judul AS category_title
        FROM article
        INNER JOIN category ON article.category_id = category.id";

// Tambahkan kondisi WHERE untuk pencarian jika query tidak kosong
if (!empty($query)) {
    $sql .= " WHERE article.title LIKE '%$query%' OR article.description LIKE '%$query%' OR article.author LIKE '%$query%'";
}

// Tambahkan kondisi untuk memfilter berdasarkan kategori
$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';
if (!empty($category_filter)) {
    $sql .= (!empty($query) ? " AND" : " WHERE") . " article.category_id = " . $category_filter;
}

// Ubah query untuk non-admin dan tambahkan ORDER BY untuk mengurutkan artikel
if ($is_admin == 1) {
    $sql .= (!empty($query) || !empty($category_filter) ? " AND" : " WHERE") . " article.author = '" . $user['name'] . "'";
} else {
    $sql .= " ORDER BY article.created_at DESC";
}

$total_search_articles_query = "SELECT COUNT(*) AS total FROM article";
if (!empty($query)) {
    $total_search_articles_query .= " WHERE title LIKE '%$query%' OR description LIKE '%$query%' OR author LIKE '%$query%'";
}
$total_search_articles_result = $conn->query($total_search_articles_query);
$total_search_articles_row = $total_search_articles_result->fetch_assoc();
$total_search_articles = $total_search_articles_row['total'];

$total_search_pages = ceil($total_search_articles / $items_per_page);

$offset = ($current_page - 1) * $items_per_page;

if (isset($_SESSION['category_filter'])) {
    $category_filter = $_SESSION['category_filter'];
} else {
    $category_filter = '';
}

$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';

if (!empty($category_filter)) {
    $_SESSION['category_filter'] = $category_filter;
} else {
    unset($_SESSION['category_filter']);
}

if ($_GET && isset($_GET['id'])) {
    $sql_delete = "DELETE FROM article WHERE id='" . $_GET['id'] . "'";

    if ($conn->query($sql_delete) === TRUE) {
        header("Location: /pages/home/index.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if ($_POST && isset($_POST['logout'])) {
    session_destroy();
    header("Location: /pages/login/blogspot.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/index.css?v=2.2">
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
            <button id="add-article-btn">+ Add New</button>
        </a>

        <?php
        if ($is_admin == 0) {
            echo '<a href="user.php" style="text-decoration: none;"><button id="user-btn">User</button></a>';
        }
        ?>

        <?php
        if ($is_admin == 0) {
            echo '<a href="category.php" style="text-decoration: none;"><button id="category-btn">Category</button></a>';
        }
        ?>

        <a href="dashboard.php" style="text-decoration: none;"><button id="dashboard-btn">Dashboard</button></a>

        <form id="articleFilterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
            <div class="search-container">
                <input name="query" type="text" placeholder="Search..." value="<?php echo isset($query) ? htmlspecialchars($query) : ''; ?>">
                <select name="category_filter" id="categoryFilter">
                    <option value="">Filter Article</option>
                    <?php
                    $category_query = "SELECT * FROM category";
                    $category_result = $conn->query($category_query);
                    if ($category_result->num_rows > 0) {
                        while ($category_row = $category_result->fetch_assoc()) {
                            echo '<option value="' . $category_row['id'] . '"' . ($category_filter == $category_row['id'] ? ' selected' : '') . '>' . $category_row['judul'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </form>

        <table id="articles-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th style="width: 153px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql .= " LIMIT $items_per_page OFFSET $offset";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                            <tr>
                                <td>' . $row['id'] . '</td>
                                <td>' . (strlen($row['title']) > 50 ? substr($row['title'], 0, 35) . '...' : $row['title']) . '</td>
                                <td>' . (strlen($row['description']) > 200 ? substr($row['description'], 0, 100) . '...' : $row['description']) . '</td>
                                <td>' . $row['author'] . '</td>
                                <td>' . $row['category_title'] . '</td>
                                <td><img src="' . $row['image'] . '" style="width: 100px;"></td>
                                <td>' . $row['created_at'] . '</td>
                                <td>' . $row['updated_at'] . '</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="form_view.php?login=1&id=' . $row['id'] . '" id="button-view">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="form_edit.php?id=' . $row['id'] . '" id="button-edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="?id=' . $row['id'] . '" id="button-hapus" onclick="return confirmDelete()">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        ';
                    }
                } else {
                    echo "
                        <tr>
                            <td colspan='9' style='text-align: center'>Data not found.</td>
                        </tr>
                        ";
                }
                ?>
            </tbody>
        </table>

        <?php
        $show_pagination = false;

        // Menentukan apakah pagination perlu ditampilkan
        if ($total_search_articles > $items_per_page) {
            $show_pagination = true;
            if (ceil($total_search_articles / $items_per_page) === 1) {
                $show_pagination = false; // Jika hanya satu halaman, tidak perlu menampilkan pagination
            }
        }

        // Jika hanya filter saja, jangan tampilkan pagination
        if (!empty($category_filter) && empty($query)) {
            $show_pagination = false;
        }
        ?>

        <?php if ($show_pagination) : ?>
            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&query=<?php echo urlencode($query); ?>&category_filter=<?php echo $category_filter; ?>">Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_search_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>&query=<?php echo urlencode($query); ?>&category_filter=<?php echo $category_filter; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_search_pages) : ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&query=<?php echo urlencode($query); ?>&category_filter=<?php echo $category_filter; ?>">Selanjutnya</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>



    </div>
    <script>
        document.getElementById("categoryFilter").addEventListener("change", function() {
            document.getElementById("articleFilterForm").submit();
        });

        document.getElementById("profileDropdown").addEventListener("click", function() {
            var dropdownContent = this.getElementsByClassName("dropdown-content")[0];
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });

        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus artikel ini?");
        }
    </script>
</body>

</html>