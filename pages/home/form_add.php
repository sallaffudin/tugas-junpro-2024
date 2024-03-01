<?php
session_start();
include('../../config/connection.php');

if (count($_SESSION) < 1) {
    header("Location: /pages/login");
    exit();
}

$user = $_SESSION['user'];

// Terima parameter simpan dari URL
$simpan = isset($_POST['simpan']) ? $_POST['simpan'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $description = str_replace(array("\r\n", "\r", "\n"), "\n", $description);
    $author = mysqli_real_escape_string($conn, $user['name']);

    // Mengambil tanggal dari form dan memformatnya
    $date = $_POST['date'];
    $date_formatted = date('Y-m-d H:i:s', strtotime($date)); // Mengubah format tanggal menjadi format MySQL

    // Validasi judul artikel dan tanggal
    if (empty($title) || empty($date)) {
        echo "<script>alert('Judul artikel dan tanggal wajib diisi');</script>";
        exit();
    }

    // Code for file upload
    $targetDirectory = "../../uploads/"; // Update path
    $targetFile = $targetDirectory . basename($_FILES["hero-image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check file size
    if ($_FILES["hero-image"]["size"] > 2000000) { // Ubah ukuran maksimum menjadi 2MB
        echo "<script>alert('Sorry, your file is too large. Maximum file size is 2MB.');</script>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.');</script>";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["hero-image"]["tmp_name"], $targetFile)) {
            // echo "<script>alert('The file " . htmlspecialchars(basename($_FILES["hero-image"]["name"])) . " has been uploaded.');</script>"; // Hapus notifikasi upload
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
    }

    if ($uploadOk == 1) {
        // SQL query to insert data
        $sql = "INSERT INTO tugas_junpro.article (title, description, author, created_at, image) VALUES (
            '$title',
            '$description',
            '$author',
            '$date_formatted',
            '$targetFile'
        )";

        if ($conn->query($sql) === TRUE) {
            // Redirect ke halaman indeks setelah berhasil disimpan dengan parameter simpan
            header("Location: index.php?simpan=1");
            exit();
        } else {
            // Tampilkan pesan error jika ada masalah dengan eksekusi query SQL
            echo "Error: " . $conn->error;
        }
    }
}
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
            <input type="text" id="title" name="title" required>
            <div id="title-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <label for="date">Tanggal Publish:</label>
            <input type="date" id="date" name="date" required>
            <div id="date-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <label for="hero-image">Hero Image:</label>
            <input type="file" id="hero-image" name="hero-image" required>
            <div id="image-error" class="error-message"></div> <!-- Pesan kesalahan -->
        </div>
        <div class="form-group">
            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" rows="4" cols="50" required></textarea>
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

    // Event listener untuk validasi panjang judul artikel
    document.getElementById('title').addEventListener('input', function() {
        var titleError = document.getElementById('title-error');
        var title = this.value.trim();
        if (title.length > 100) {
            titleError.textContent = "Judul artikel tidak boleh melebihi 100 karakter.";
        } else {
            titleError.textContent = ""; // Menghapus pesan kesalahan jika valid
        }
    });

    // Validasi saat tombol submit ditekan
    document.getElementById('submit-button').onclick = function(event) {
        var titleInput = document.getElementById('title');
        var dateInput = document.getElementById('date');
        var descriptionInput = document.getElementById('description');
        var titleError = document.getElementById('title-error');
        var dateError = document.getElementById('date-error');
        var descriptionError = document.getElementById('description-error');
        var imageError = document.getElementById('image-error');
        var title = titleInput.value.trim();
        var date = dateInput.value.trim();
        var description = descriptionInput.value.trim();
        var image = document.getElementById('hero-image').value.trim();
        var valid = true;

        // Validasi judul artikel
        if (title === "") {
            titleError.textContent = "Judul artikel wajib diisi.";
            valid = false;
        } else if (title.length > 100) {
            titleError.textContent = "Judul artikel tidak boleh melebihi 100 karakter.";
            valid = false;
        }

        // Validasi tanggal
        if (date === "") {
            dateError.textContent = "Tanggal wajib diisi.";
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