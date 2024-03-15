<?php
include('../../config/connection.php');

// Function to get article count per category and the categories with the highest count for a specific month
function getArticleCountByCategory($conn, $month) {
    $article_count_by_category = [];
    $most_articles_categories = []; // Array to store categories with the highest count

    // SQL query to get article count per category for a specific month
    $sql = "SELECT c.judul AS category, COUNT(a.id) AS jumlah
            FROM category c
            LEFT JOIN article a ON c.id = a.category_id
            WHERE MONTH(a.created_at) = $month
            GROUP BY c.id";
    $result = $conn->query($sql);

    // Check if query was successful
    if ($result) {
        // Fetch associative array
        while ($row = $result->fetch_assoc()) {
            $article_count_by_category[] = $row;
        }

        // Find the category/categories with the highest count
        $max_count = 0;
        foreach ($article_count_by_category as $category) {
            if ($category['jumlah'] > $max_count) {
                $max_count = $category['jumlah'];
                $most_articles_categories = [$category['category']];
            } elseif ($category['jumlah'] == $max_count) {
                // If there are multiple categories with the same highest count, add them to the array
                $most_articles_categories[] = $category['category'];
            }
        }
    }

    return array($article_count_by_category, $most_articles_categories);
}

// Set the month you want to get the data for
$month = date('m'); // Current month

// Get article count per category and the categories with the highest count for the specified month
list($article_count_by_category, $most_articles_categories) = getArticleCountByCategory($conn, $month);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css?v=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <!-- Tombol kembali -->
            <a href="../home/index.php" class="back-button"><i class="bi bi-arrow-left"></i> Back</a>
            <h1>Dashboard</h1>
        </header>
        <main class="main">
            <section class="section section-2">
                <h2>Jumlah Artikel per Kategori</h2>
                <div style="overflow-x:auto;">
                    <?php if (empty($article_count_by_category)) { ?>
                        <table style="width:100%">
                            <tr>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                            </tr>
                            <tr class="data-not-found">
                                <td colspan="2">Data not found.</td>
                            </tr>
                        </table>
                    <?php } else { ?>
                        <table style="width:100%">
                            <tr>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                            </tr>
                            <?php foreach ($article_count_by_category as $row) { ?>
                                <tr>
                                    <td><?php echo $row['category']; ?></td>
                                    <td><?php echo $row['jumlah']; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php if (!empty($most_articles_categories)) { ?>
                            <?php if (count($most_articles_categories) == 1) { ?>
                                <p>Kategori dengan jumlah artikel terbanyak pada bulan <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?>: <?php echo $most_articles_categories[0]; ?></p>
                            <?php } else { ?>
                                <p>Kategori-kategori dengan jumlah artikel terbanyak pada bulan <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?>:</p>
                                <ul>
                                    <?php foreach ($most_articles_categories as $category) { ?>
                                        <li><?php echo $category; ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </section>
            
            <!-- Tambahkan tabel "Penulis Bulan Jumlah" di bawah -->
            <section class="section section-2">
                <h2>Jumlah Penulis Paling Aktif per Bulan</h2>
                <div style="overflow-x:auto;">
                    <?php
                    // Query untuk mendapatkan jumlah artikel per penulis per bulan
                    $sql = "SELECT author, MONTH(created_at) AS bulan, COUNT(*) AS jumlah_artikel
                            FROM article
                            GROUP BY author, MONTH(created_at)
                            ORDER BY jumlah_artikel DESC";
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        ?>
                        <table style="width:100%">
                            <tr>
                                <th>Penulis</th>
                                <th>Bulan</th>
                                <th>Jumlah</th>
                            </tr>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['author']; ?></td>
                                    <td><?php echo date('F', mktime(0, 0, 0, $row['bulan'], 1)); ?></td>
                                    <td><?php echo $row['jumlah_artikel']; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php
                    } else {
                        ?>
                        <table style="width:100%">
                            <tr>
                                <th>Penulis</th>
                                <th>Bulan</th>
                                <th>Jumlah</th>
                            </tr>
                            <tr class="data-not-found">
                                <td colspan="3">Data not found.</td>
                            </tr>
                        </table>
                        <?php
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
