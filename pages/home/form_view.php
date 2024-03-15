<?php
include('../../config/connection.php');

function increaseViewCount($conn, $articleId) {
    $sqlSelect = "SELECT view FROM tugas_junpro.article WHERE id = $articleId";
    $resultSelect = $conn->query($sqlSelect);
    
    if ($resultSelect && $resultSelect->num_rows > 0) {
        $row = $resultSelect->fetch_assoc();
        $currentView = $row['view'];
        
        // Update view count
        $sqlUpdate = "UPDATE tugas_junpro.article SET view = $currentView + 1 WHERE id = $articleId";
        $resultUpdate = $conn->query($sqlUpdate);
        
        if (!$resultUpdate) {
            echo "Error updating view count: " . $conn->error;
            return;
        }
    } else {
        echo "Error retrieving current view count: " . $conn->error;
        return;
    }
}

// Memanggil fungsi untuk meningkatkan jumlah view
if (isset($_GET['id'])) {
    increaseViewCount($conn, $_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/form_view.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>View Article</title>
</head>
<body>
    <div class="container">
        <div class="back-button">
            <!-- Back button with Icon -->
            <?php
            $loginLink = ($_GET['login'] == 1) ? 'index.php' : '../login/blogspot.php';
            echo '<a href="' . $loginLink . '" class="btn-back"><i class="bi bi-arrow-left"></i>Kembali</a>';
            ?>
        </div>

        <?php
        // Get article data
        if(isset($_GET['id'])) {
            $articleId = $_GET['id'];
            $sql = "SELECT *  FROM tugas_junpro.article WHERE id = $articleId LIMIT 1";
            
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Display article information
                $viewCount = $row['view'];
                echo '
                <article>
                    <p class="article-date">' . date('Y-m-d', strtotime($row['created_at'])) . ' <i class="bi bi-eye-fill"></i> ' . $viewCount . '</p>
                    <div>
                        <img src="' . $row['image'] . '">
                    </div>
                    <div>
                        <p class="title">' . $row['title'] . '</p>
                    </div>
                    <div class="separator"></div>
                    <div>
                        <p class="description">' . nl2br($row['description']) . '</p>
                    </div>
                </article>
                ';
            } else {
                echo "Article not found.";
            }
        } else {
            echo "Article ID not provided.";
        }
        ?>
    </div>
</body>
</html>
