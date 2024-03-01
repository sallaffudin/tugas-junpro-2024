<?php

include('../../config/connection.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/blogspot.css?v=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Edit Article</title>
</head>

<body>

    <div class="container">
        <!-- Tombol Kembali ke Tabel dengan Icon -->
        <?php 
        if($_GET['login']==1){
            echo' <a href="index.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>Kembali</a>';
        }else{
            echo' <a href="../login/blogspot.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>Kembali</a>';
        }
       ?>
    </div>

    <?php
    // Mendapatkan data artikel yang akan diedit
    $sql = "SELECT * FROM tugas_junpro.article WHERE id = " . $_GET['id'] . " LIMIT 1";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="container">
            <article>
                <p class="article-date">' . date('Y-m-d', strtotime($row['created_at'])) . '</p>
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
        </div>
        ';
    }
    ?>

</body>

</html>
