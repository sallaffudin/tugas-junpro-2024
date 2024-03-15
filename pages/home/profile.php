<?php
session_start();

// Include file koneksi database
include('../../config/connection.php');

$errors = array(); // Menyimpan pesan kesalahan
$success_message = ""; // Menyimpan pesan berhasil

// Cek session pengguna
if (empty($_SESSION['user'])) {
    header("Location: /pages/login");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi nama
    if (empty($_POST["name"])) {
        $errors[] = "Nama harus diisi";
    } else {
        $name = $_POST["name"];
    }

    // Validasi email
    if (empty($_POST["email"])) {
        $errors[] = "Email harus diisi";
    } else {
        $email = $_POST["email"];
        // Lakukan validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }
    }

    // Validasi kata sandi baru
    if (!empty($_POST["new_password"])) {
        $old_password = $_POST["old_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        // Validasi kata sandi lama
        if (empty($old_password)) {
            $errors[] = "Kata Sandi Lama harus diisi untuk mengubah kata sandi baru";
        } else {
            // Lakukan pengecekan kata sandi lama dengan yang ada di database
            $stored_password_hash = $user['password']; // Gantilah ini dengan cara Anda untuk mendapatkan kata sandi dari database
            if (!password_verify($old_password, $stored_password_hash)) {
                $errors[] = "Kata Sandi Lama tidak cocok";
            }
        }

        // Validasi kata sandi baru
        if (empty($new_password)) {
            $errors[] = "Kata Sandi Baru harus diisi";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "Kata Sandi Baru minimal terdiri dari 6 karakter";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Konfirmasi Kata Sandi tidak cocok dengan Kata Sandi Baru";
        }
    }

    // Jika tidak ada kesalahan, simpan perubahan nama, email, dan kata sandi
    if (empty($errors)) {
        // Simpan perubahan nama dan email ke database
        $user['name'] = $name;
        $user['email'] = $email;

        // Simpan perubahan kata sandi ke database jika dimasukkan
        if (!empty($new_password)) {
            // Enkripsi kata sandi baru
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $user['password'] = $new_password_hash;
        }

        // Simpan perubahan ke database
        // Misalnya, Anda memiliki skrip untuk memperbarui data pengguna di database, Anda harus menggantikan baris berikut sesuai dengan kebutuhan Anda:
        $userId = $user['id']; // Gantilah ini dengan id pengguna Anda dari sesi atau dari data pengguna
        $query = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $statement = $conn->prepare($query);
        $statement->bind_param("sssi", $user['name'], $user['email'], $user['password'], $userId);
        $statement->execute();

        // Update juga informasi user di session
        $_SESSION['user'] = $user;

        // Set pesan berhasil
        $success_message = "Profil berhasil diperbarui.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/profile.css?v=2.1">
    <!-- Link untuk Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>User Profile</title>
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <?php if (!empty($success_message)): ?>
            <div id="successMessage" class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form action="#" method="post" enctype="multipart/form-data" id="profileForm">
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" readonly>

            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : $user['name']; ?>" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : $user['email']; ?>" required>

            <label for="old_password">Kata Sandi Lama<span style="color: red;">*</span></label>
            <div class="password-container">
                <input type="password" id="old_password" name="old_password">
                <i class="bi bi-eye-slash toggle-password"  onclick="togglePasswordVisibility('old_password')"></i>
                <span class="error-message" id="oldPasswordError"></span>
            </div>

            <label for="new_password">Kata Sandi Baru<span style="color: red;">*</span></label>
            <div class="password-container">
                <input type="password" id="new_password" name="new_password">
                <i class="bi bi-eye-slash toggle-password" onclick="togglePasswordVisibility('new_password')"></i>
                <span class="error-message" id="newPasswordError"></span>
            </div>

            <label for="confirm_password">Konfirmasi Kata Sandi<span style="color: red;">*</span></label>
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password">
                <i class="bi bi-eye-slash toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
                <span class="error-message" id="confirmPasswordError"></span>
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            var input = document.getElementById(inputId);
            var icon = input.nextElementSibling;

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye-fill");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-fill");
                icon.classList.add("bi-eye-slash");
            }
        }

        // Fungsi untuk validasi kata sandi lama
        function validateOldPassword() {
            var oldPassword = document.getElementById("old_password").value.trim();
            var oldPasswordError = document.getElementById("oldPasswordError");

            if (oldPassword === "") {
                oldPasswordError.textContent = "Kata Sandi Lama harus diisi";
                return false;
            } else {
                oldPasswordError.textContent = "";
                return true;
            }
        }

        // Fungsi untuk validasi kata sandi baru
        function validateNewPassword() {
            var newPassword = document.getElementById("new_password").value.trim();
            var newPasswordError = document.getElementById("newPasswordError");

            if (newPassword === "") {
                newPasswordError.textContent = "Kata Sandi Baru harus diisi";
                return false;
            } else if (newPassword.length < 6) {
                newPasswordError.textContent = "Kata Sandi Baru minimal terdiri dari 6 karakter";
                return false;
            } else {
                newPasswordError.textContent = "";
                return true;
            }
        }

        // Fungsi untuk validasi konfirmasi kata sandi
        function validateConfirmPassword() {
            var newPassword = document.getElementById("new_password").value.trim();
            var confirmPassword = document.getElementById("confirm_password").value.trim();
            var confirmPasswordError = document.getElementById("confirmPasswordError");

            if (confirmPassword === "") {
                confirmPasswordError.textContent = "Konfirmasi Kata Sandi harus diisi";
                return false;
            } else if (confirmPassword !== newPassword) {
                confirmPasswordError.textContent = "Konfirmasi Kata Sandi tidak cocok dengan Kata Sandi Baru";
                return false;
            } else {
                confirmPasswordError.textContent = "";
                return true;
            }
        }

        // Menambahkan event listener untuk setiap input
        document.getElementById("old_password").addEventListener("input", function() {
            validateOldPassword();
        });

        document.getElementById("new_password").addEventListener("input", function() {
            validateNewPassword();
        });

        document.getElementById("confirm_password").addEventListener("input", function() {
            validateConfirmPassword();
        });

        document.getElementById("profileForm").addEventListener("submit", function(event) {
            // Validasi saat form disubmit
            var valid = true;
            if (!validateOldPassword()) valid = false;
            if (!validateNewPassword()) valid = false;
            if (!validateConfirmPassword()) valid = false;

            if (!valid) {
                event.preventDefault();
            }
        });

        // Fungsi untuk mengarahkan pengguna ke halaman index setelah 5 detik
        function redirectAfterDelay(url, delay) {
            setTimeout(function() {
                window.location.href = url;
            }, delay);
        }

        // Cek apakah ada pesan sukses, jika ada, arahkan pengguna setelah 5 detik
        var successMessage = document.getElementById("successMessage");
        if (successMessage) {
            redirectAfterDelay("/pages/home/index.php", 5000); // Ganti "/index.php" dengan URL tujuan Anda
        }
    </script>
</body>
</html>
