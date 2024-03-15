<?php
include('../../config/connection.php');

$name = $username = $email = $password = $confirm_password = "";
$name_err = $username_err = $email_err = $password_err = $confirm_password_err = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input name
    if (empty(trim($_POST["Name"]))) {
        $name_err = "Name is required.";
    } else {
        $name = trim($_POST["Name"]);
    }

    // Validasi input username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Username is required.";
    } else {
        $username = trim($_POST["username"]);
        // Check if username already exists
        $check_username_sql = "SELECT id FROM users WHERE username = '$username'";
        $check_username_result = $conn->query($check_username_sql);
        if ($check_username_result && $check_username_result->num_rows > 0) {
            $username_err = "Username already exists.";
        }
    }

    // Validasi input email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Email is required.";
    } else {
        $email = trim($_POST["email"]);
        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        } else {
            // Check if email already exists
            $check_email_sql = "SELECT id FROM users WHERE email = '$email'";
            $check_email_result = $conn->query($check_email_sql);
            if ($check_email_result && $check_email_result->num_rows > 0) {
                $email_err = "Email already exists.";
            }
        }
    }

    // Validasi input password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Password is required.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must have at least 8 characters.";
    } elseif (!preg_match("/[A-Z]/", $_POST["password"])) {
        $password_err = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match("/[0-9]/", $_POST["password"])) {
        $password_err = "Password must contain at least one number.";
    } elseif (!preg_match("/[!@#$%^&*()\-_=+{};:,<.>]/", $_POST["password"])) {
        $password_err = "Password must contain at least one special character.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validasi input confirm password
    if (empty(trim($_POST["confirm-password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm-password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Cek apakah tidak ada error validasi sebelum memproses data
    if (empty($name_err) && empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $is_admin = 1; // Set nilai is_admin ke 1 untuk pengguna biasa

        // Menyiapkan statement SQL untuk dieksekusi
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Menambahkan user dengan status aktif
        $users_status = 1; // 1 untuk aktif
        $sql = "INSERT INTO users (name, username, email, password, created_at, updated_at, is_admin, users_status) 
                VALUES ('$name', '$username', '$email', '$hashed_password', '$created_at', '$updated_at', '$is_admin', '$users_status')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Registrasi berhasil!";
            // Reset nilai input setelah berhasil submit
            $name = $username = $email = $password = $confirm_password = "";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../assets/css/registrasi.css?v=2.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            if (password != confirmPassword) {
                alert("Konfirmasi password tidak sesuai.");
                return false;
            }
            return true;
        }

        function validateName() {
            var name = document.getElementById("Name").value;
            if (name.trim() === "") {
                document.getElementById("name-error").textContent = "Name is required.";
            } else {
                document.getElementById("name-error").textContent = "";
            }
        }

        function validateUsername() {
            var username = document.getElementById("username").value;
            if (username.trim() === "") {
                document.getElementById("username-error").textContent = "Username is required.";
            } else {
                document.getElementById("username-error").textContent = "";
            }
        }

        function validateEmail() {
            var email = document.getElementById("email").value;
            if (email.trim() === "") {
                document.getElementById("email-error").textContent = "Email is required.";
            } else {
                document.getElementById("email-error").textContent = "";
            }
        }

        function validatePassword() {
            var password = document.getElementById("password").value;
            if (password.trim() === "") {
                document.getElementById("password-error").textContent = "Password is required.";
            } else {
                document.getElementById("password-error").textContent = "";
            }
        }

        function validateConfirmPassword() {
            var confirmPassword = document.getElementById("confirm-password").value;
            var password = document.getElementById("password").value;
            if (confirmPassword.trim() === "") {
                document.getElementById("confirm-password-error").textContent = "Please confirm password.";
            } else if (confirmPassword !== password) {
                document.getElementById("confirm-password-error").textContent = "Password did not match.";
            } else {
                document.getElementById("confirm-password-error").textContent = "";
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($success_message !== "") { ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php } ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()" novalidate>
            <label for="Name">Name:</label>
            <input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($name); ?>" oninput="validateName()" required>
            <span class="error" id="name-error"><?php echo $name_err; ?></span>
            <br>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" oninput="validateUsername()" required>
            <span class="error" id="username-error"><?php echo $username_err; ?></span>
            <br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" oninput="validateEmail()" required>
            <span class="error" id="email-error"><?php echo $email_err; ?></span>
            <br>

            <label for="password">Password:</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" oninput="validatePassword()" required>
                <i id="togglePassword" class="bi bi-eye-slash" style="position: absolute; right: 10px; top: 42%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
            </div>
            <span class="error" id="password-error"><?php echo $password_err; ?></span>
            <br>

            <label for="confirm-password">Confirm Password:</label>
            <div style="position: relative;">
                <input type="password" id="confirm-password" name="confirm-password" oninput="validateConfirmPassword()" required>
                <i id="toggleConfirmPassword" class="bi bi-eye-slash" style="position: absolute; right: 10px; top: 42%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('confirm-password', 'toggleConfirmPassword')"></i>
            </div>
            <span class="error" id="confirm-password-error"><?php echo $confirm_password_err; ?></span>
            <br>

            <button type="submit">Register</button>
        </form>

        <p>Sudah punya akun? <a href="../login/login.php">Login disini</a></p>
    </div>
</body>

</html>
