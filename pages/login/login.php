<?php include('../../config/connection.php'); ?>

<?php

$invalid_login = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email='$email'  LIMIT 1";
        $result = $conn->query($sql);
        if ($result && $result->num_rows == 1) {
            $data = $result->fetch_assoc();
            if (!password_verify($password, $data['passwrod'])){
                $invalid_login = true;
            }
            // User authenticated, set session variables
            session_start();
            $_SESSION['user'] = $data;
            header("Location: /pages/home/index.php");
            exit(); // Terminate script after redirection
        } else {
            $invalid_login = true;
        }
    } else {
        $invalid_login = true; // Jika email dan password tidak tersedia
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <form action="" method="post" id="loginForm">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <div id="emailError" style="color: red;"></div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <div id="passwordError" style="color: red;"></div>
            <button type="submit" id="loginButton">Login</button>
        </form>
        <?php
        if ($invalid_login) {
            echo '<p style="color: red">User or password mismatch</p>';
        }
        ?>
        <p>Belum punya akun? <a href="../register/registrasi.php">Daftar disini</a></p>
    </div>
    <script>
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const loginButton = document.getElementById('loginButton');

        loginButton.addEventListener('click', function(event) {
            let valid = true;

            // Validasi Email
            if (!emailInput.value.trim()) {
                emailError.textContent = 'Email harus diisi';
                emailError.style.color = 'red'; // Mengatur warna teks pesan kesalahan menjadi merah
                valid = false;
            } else {
                emailError.textContent = '';
            }

            // Validasi Password
            if (!passwordInput.value.trim()) {
                passwordError.textContent = 'Password harus diisi';
                passwordError.style.color = 'red'; // Mengatur warna teks pesan kesalahan menjadi merah
                valid = false;
            } else {
                passwordError.textContent = '';
            }

            if (!valid) {
                event.preventDefault(); // Mencegah pengiriman form jika tidak valid
            }
        });

        // Menghapus pesan kesalahan saat pengguna mulai mengisi kembali input
        emailInput.addEventListener('input', function() {
            if (emailInput.value.trim()) {
                emailError.textContent = '';
            }
        });

        passwordInput.addEventListener('input', function() {
            if (passwordInput.value.trim()) {
                passwordError.textContent = '';
            }
        });
    </script>
</body>

</html>
