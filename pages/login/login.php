<?php 
session_start();
include('../../config/connection.php'); 

$invalid_login = isset($_SESSION['invalid_login']) ? $_SESSION['invalid_login'] : false;
unset($_SESSION['invalid_login']); // Clear the session variable after displaying the message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email='$email'  LIMIT 1";
        $result = $conn->query($sql);
        if ($result && $result->num_rows == 1) {
            $data = $result->fetch_assoc();
            if (password_verify($password, $data['password'])) {
                $_SESSION['user'] = $data; // Set session data for the logged-in user
                header("Location: /pages/home/index.php");
                exit();
            } else {
                $_SESSION['invalid_login'] = true;
                header("Location: ".$_SERVER['PHP_SELF']); // Redirect to refresh the page
                exit();
            }
        } else {
            $_SESSION['invalid_login'] = true;
            header("Location: ".$_SERVER['PHP_SELF']); // Redirect to refresh the page
            exit();
        }
    } else {
        $_SESSION['invalid_login'] = true;
        header("Location: ".$_SERVER['PHP_SELF']); // Redirect to refresh the page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/login.css?v=1.2">
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <?php
        if ($invalid_login) {
            echo '<p style="color: red">User or password mismatch</p>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="loginForm">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <div id="emailError" style="color: red;"></div>
            <label for="password">Password:</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" required>
                <i id="togglePassword" class="bi bi-eye-slash" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); cursor: pointer;"></i>
            </div>
            <div id="passwordError" style="color: red;"></div>
            <button type="submit" id="loginButton">Login</button>
        </form>
        <p>Belum punya akun? <a href="../register/registrasi.php">Daftar disini</a></p>
    </div>
    <script>
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bi-eye-slash');
            this.classList.toggle('bi-eye');
        });

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
