<?php
include('../../config/connection.php');

// Inisialisasi variabel pencarian
$query = '';
$category_filter = '';
if (isset($_GET['query'])) {
    $query = $_GET['query'];
}
if (isset($_GET['category_filter'])) {
    $category_filter = $_GET['category_filter'];
}

// SQL query
$sql = "SELECT article.*, category.judul AS category_title
        FROM article
        LEFT JOIN category ON article.category_id = category.id";
if (!empty($query) || !empty($category_filter)) {
    $sql .= " WHERE";
    if (!empty($query)) {
        $sql .= " article.title LIKE '%$query%' OR article.description LIKE '%$query%' OR article.author LIKE '%$query%'";
        if (!empty($category_filter)) {
            $sql .= " AND";
        }
    }
    if (!empty($category_filter)) {
        $sql .= " article.category_id = $category_filter";
    }
}

// Execute query
$result = $conn->query($sql);

// Tombol "btn-back" ditekan
if ($_GET && isset($_GET['btn-back'])) {
    session_destroy();
    header("Location: /pages/login/blogspot.php");
    exit(); // Pastikan untuk keluar setelah mengarahkan ke halaman lain
}

// Pagination
$items_per_page = 4; // Ganti dengan jumlah artikel per halaman yang diinginkan
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Hitung total artikel berdasarkan pencarian
$total_articles_query = "SELECT COUNT(*) AS total FROM article";
if (!empty($query) || !empty($category_filter)) {
    $total_articles_query .= " WHERE";
    if (!empty($query)) {
        $total_articles_query .= " article.title LIKE '%$query%' OR article.description LIKE '%$query%' OR article.author LIKE '%$query%'";
        if (!empty($category_filter)) {
            $total_articles_query .= " AND";
        }
    }
    if (!empty($category_filter)) {
        $total_articles_query .= " article.category_id = $category_filter";
    }
}
$total_articles_result = $conn->query($total_articles_query);
$total_articles_row = $total_articles_result->fetch_assoc();
$total_articles = $total_articles_row['total'];

// Hitung total halaman
$total_pages = ceil($total_articles / $items_per_page);

// Hitung offset
$offset = ($current_page - 1) * $items_per_page;

// Sesuaikan query dengan offset dan limit
$sql .= " LIMIT $items_per_page OFFSET $offset";

// Execute modified query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/blogspot.css?v=1.6">
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
        <!-- Form untuk pencarian dan filter -->
        <form id="articleFilterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
            <div class="search-container">
                <input name="query" type="text" placeholder="Search..." value="<?php echo isset($query) ? htmlspecialchars($query) : ''; ?>">
                <select name="category_filter" id="categoryFilter" onchange="categoryFilterChanged()">
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
                    <th>Title</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Category</th> <!-- Tambahkan kolom kategori -->
                    <th>Image</th>
                    <th style="width: 90px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                            <tr>
                                <td>' . (strlen($row['title']) > 50 ? substr($row['title'], 0, 50) . '...' : $row['title']) . '</td>
                                <td>' . (strlen($row['description']) > 200 ? substr($row['description'], 0, 200) . '...' : $row['description']) . '</td>
                                <td>' . $row['author'] . '</td>
                                <td>' . $row['category_title'] . '</td> <!-- Tampilkan kategori -->
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

        <!-- Pagination -->
        <?php if ($total_pages > 1) : ?>
            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&query=<?php echo urlencode($query); ?>&category_filter=<?php echo urlencode($category_filter); ?>">Sebelumnya</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>&query=<?php echo urlencode($query); ?>&category_filter=<?php echo urlencode($category_filter); ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($current_page < $total_pages) : ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&query=<?php echo urlencode($query); ?>&category_filter=<?php echo urlencode($category_filter); ?>">Selanjutnya</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        // Fungsi untuk mengubah URL ketika dropdown kategori diubah
        function categoryFilterChanged() {
            var categoryFilter = document.getElementById('categoryFilter').value;
            var queryString = window.location.search;
            var urlParams = new URLSearchParams(queryString);

            if (categoryFilter) {
                urlParams.set('category_filter', categoryFilter);
            } else {
                urlParams.delete('category_filter');
            }

            window.location.search = urlParams.toString();
        }
    </script>
</body>

</html>
