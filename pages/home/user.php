<?php
include('../../config/connection.php');

function getUsers($searchQuery = '') {
    global $conn;
    $users = array();

    $query = "SELECT users.*, users_status.name AS status_name 
              FROM users 
              LEFT JOIN users_status ON users.users_status = users_status.users_status_id";

    if (!empty($searchQuery)) {
        $query .= " WHERE (users.username LIKE '%" . $searchQuery . "%' OR users.name LIKE '%" . $searchQuery . "%' OR users.email LIKE '%" . $searchQuery . "%')";
    }

    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    } else {
        echo "Error retrieving users: " . mysqli_error($conn);
    }

    mysqli_free_result($result);

    return $users;
}

// Function to get user data by ID
function getUserById($userId, $conn) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

$query = isset($_POST['query']) ? $_POST['query'] : '';
$usersData = getUsers($query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit-user-id'])) {
    $userId = $_POST['edit-user-id'];

    // Ambil data pengguna yang ingin diedit
    $editUserData = getUserById($userId, $conn);
    $editUserDataJSON = json_encode($editUserData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/user.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <a href="../home/index.php" class="back-button"><i class="bi bi-arrow-left"></i> Back</a>
    <div class="container">
        <h1>Manage Users</h1>
        <form action="" method="POST">
            <div class="search-container">
                <input name="query" type="text" placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>">
            </div>
        </form>
        <table id="users-table">
            <thead>
                <tr>
                    <th style="width: 20px;">ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th style="width: 50px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($usersData) > 0): ?>
                    <?php foreach ($usersData as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['status_name']; ?></td>
                            <td class="action-buttons">
                                <a href="#" class="edit" onclick="openEditModal(<?php echo $user['id']; ?>)"><i class="bi bi-pencil-fill"></i></a>
                                <!-- Icon Edit -->
                                <a href="#" class="delete"><i class="bi bi-trash"></i></a>
                                <!-- Icon Hapus -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Data not found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit User</h2>
            <form action="edit_user.php" method="POST" id="editForm">
                <input type="hidden" id="edit-user-id" name="edit-user-id" value="">
                <label for="edit-username">Username:</label><br>
                <input type="text" id="edit-username" name="edit-username" value=""><br>
                <label for="edit-name">Name:</label><br>
                <input type="text" id="edit-name" name="edit-name" value=""><br>
                <label for="edit-email">Email:</label><br>
                <input type="email" id="edit-email" name="edit-email" value=""><br>
                <label for="edit-status">Status:</label><br>
                <select id="edit-status" name="edit-status">
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                    <option value="3">Locked</option>
                </select><br>
                <label for="edit-password">New Password:</label><br>
                <div class="password-input">
                    <input type="password" id="edit-password" name="edit-password" value="">
                    <i class="bi bi-eye-slash" id="toggle-password"></i>
                </div><br>
                <label for="edit-confirm-password">Confirm New Password:</label><br>
                <div class="password-input">
                    <input type="password" id="edit-confirm-password" name="edit-confirm-password" value="">
                    <i class="bi bi-eye-slash" id="toggle-confirm-password"></i>
                </div><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>

    <script>
    function openEditModal(userId) {
        var modal = document.getElementById("editModal");
        modal.style.display = "block";

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var userData = JSON.parse(xhr.responseText);
                    document.getElementById("edit-user-id").value = userData.id;
                    document.getElementById("edit-username").value = userData.username;
                    document.getElementById("edit-name").value = userData.name;
                    document.getElementById("edit-email").value = userData.email;
                    // Perbarui nilai select status sesuai dengan status_name dari data pengguna
                    document.getElementById("edit-status").value = userData.users_status;
                    var statusName = userData.status_name;
                    var statusSelect = document.getElementById("edit-status");
                    for (var i = 0; i < statusSelect.options.length; i++) {
                        if (statusSelect.options[i].text.toLowerCase() === statusName.toLowerCase()) {
                            statusSelect.selectedIndex = i;
                            break;
                        }
                    }
                } else {
                    console.error('Error fetching user data: ' + xhr.status);
                }
            }
        };
        xhr.open("GET", "edit_user.php?id=" + userId, true);
        xhr.send();
    }

    function closeEditModal() {
        var modal = document.getElementById("editModal");
        modal.style.display = "none";
    }

    // Tampilkan atau sembunyikan kata sandi
    document.getElementById("toggle-password").addEventListener("click", function() {
        var passwordField = document.getElementById("edit-password");
        var icon = document.getElementById("toggle-password");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            passwordField.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    });

    document.getElementById("toggle-confirm-password").addEventListener("click", function() {
        var confirmPasswordField = document.getElementById("edit-confirm-password");
        var icon = document.getElementById("toggle-confirm-password");

        if (confirmPasswordField.type === "password") {
            confirmPasswordField.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            confirmPasswordField.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    });

    // Tangani pengiriman formulir secara asinkron
    document.getElementById("editForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Mencegah pengiriman formulir default
        var formData = new FormData(this);

        fetch("edit_user.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success); // Tampilkan notifikasi berhasil
                closeEditModal(); // Tutup modal
                window.location.href = "user.php"; // Alihkan kembali ke halaman user.php
            } else {
                alert(data.error); // Tampilkan notifikasi gagal jika ada
            }
        })
        .catch(error => {
            console.error("Error updating user data:", error);
            alert("An error occurred. Please try again later.");
        });
    });
    </script>
</body>
</html>
