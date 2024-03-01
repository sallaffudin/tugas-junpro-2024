<?php
session_start();
include('../../config/connection.php');

if (count($_SESSION) < 1) {
    return header("Location: /pages/login");
}

// Terima parameter simpan dari URL
$simpan = isset($_POST['simpan']) ? $_POST['simpan'] : '';

if (count($_POST) > 0) {
    // Mendapatkan tanggal dan waktu saat ini
    $date_now = date('Y-m-d H:i:s');

    // Menggunakan prepared statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("UPDATE article 
                            SET title=?, description=?, updated_at=?, image=?
                            WHERE id=?");

    $stmt->bind_param("ssssi", $title, $description, $date_now, $image, $article_id);

    $title = $_POST['title'];
    $description = $_POST['description'];
    $article_id = $_GET['id'];

    // Menyimpan file gambar jika dipilih untuk diunggah
    if ($_FILES["hero-image"]["name"] != '') {
        $targetDirectory = "../../uploads/";
        $targetFile = $targetDirectory . basename($_FILES["hero-image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($targetFile)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["hero-image"]["size"] > 2000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Move uploaded file to destination directory
        if (move_uploaded_file($_FILES["hero-image"]["tmp_name"], $targetFile)) {
            $image = $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
            $image = ''; // Set image to empty string to avoid updating it in database
        }
    } else {
        $image = ''; // Set image to empty string if no new image is uploaded
    }

    // Execute SQL statement
    if ($stmt->execute()) {
        // Redirect to index.php with simpan parameter set to 1
        header("Location: index.php?simpan=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Mendapatkan data artikel yang akan diedit
$sql = "SELECT * FROM tugas_junpro.article WHERE id = " . $_GET['id'] . " LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc(); // Ambil data artikel dari hasil query
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../assets/css/form.css?v=2.0">
</head>

<body>
    <form id="article-form" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Judul Artikel:</label>
            <input type="text" id="title" name="title" value="<?php echo $row['title']; ?>" required>
            <div id="title-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <label for="date">Tanggal Publish:</label>
            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d', strtotime($row['created_at'])); ?>" required>
            <div id="date-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <label for="hero-image">Hero Image:</label>
            <input type="file" id="hero-image" name="hero-image" required>
            <div id="image-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" rows="4" cols="50" required><?php echo $row['description']; ?></textarea>
            <div id="description-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <button type="submit" id="submit-button">Simpan</button> <!-- Tombol submit -->
            <a href="index.php"><button type="button">Back</button></a>
        </div>
    </form>

    <script>
        // Event listener untuk gambar saat dipilih
        document.getElementById('hero-image').addEventListener('change', function() {
            var errorMessage = document.getElementById('image-error');
            var inputFile = this;
            if (inputFile.files.length === 0) {
                errorMessage.textContent = "Gambar harus dipilih.";
                return;
            }
            var fileSize = inputFile.files[0].size;
            if (fileSize > 2000000) { // Ukuran maksimum 2MB
                errorMessage.textContent = "Ukuran gambar tidak boleh lebih dari 2MB.";
            } else {
                errorMessage.textContent = "";
            }
        });

        // Event listener untuk setiap elemen input
        document.querySelectorAll('input, textarea').forEach(function(el) {
            el.addEventListener('input', function() {
                var errorMessage = this.parentNode.querySelector('.error-message');
                errorMessage.textContent = ""; // Menghapus pesan kesalahan saat input berubah
            });
        });

        document.getElementById('submit-button').onclick = function(event) {
            var titleInput = document.getElementById('title');
            var dateInput = document.getElementById('date');
            var descriptionInput = document.getElementById('description');
            var titleError = document.getElementById('title-error');
            var dateError = document.getElementById('date-error');
            var descriptionError = document.getElementById('description-error');
            var imageError = document.getElementById('image-error');
            var title = titleInput.value.trim();
            var description = descriptionInput.value.trim();
            var image = document.getElementById('hero-image').value.trim();
            var valid = true;

            // Validasi judul artikel
            if (title === "") {
                titleError.textContent = "Judul artikel wajib diisi.";
                valid = false;
            }

            // Validasi deskripsi
            if (description === "") {
                descriptionError.textContent = "Deskripsi wajib diisi.";
                valid = false;
            }

            // Validasi gambar
            if (image === "") {
                imageError.textContent = "Gambar wajib dipilih.";
                valid = false;
            }

            if (!valid) {
                event.preventDefault(); // Mencegah pengiriman formulir jika validasi gagal
            }

            return valid;
        };
    </script>
</body>

</html>
