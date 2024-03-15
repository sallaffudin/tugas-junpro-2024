<?php
session_start();
include('../../config/connection.php');

// Function to get user data by ID
function getUserById($userId, $conn) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Function to update user data including password
function updateUserWithPassword($userId, $username, $name, $email, $password, $status, $conn) {
    // Check if password is provided and not empty
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users 
                  SET username = ?, name = ?, email = ?, password = ?, users_status = ?
                  WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssii", $username, $name, $email, $hashedPassword, $status, $userId);
    } else {
        // If password is not provided, update without changing the password
        $query = "UPDATE users 
                  SET username = ?, name = ?, email = ?, users_status = ?
                  WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssii", $username, $name, $email, $status, $userId);
    }
    return mysqli_stmt_execute($stmt);
}


// Check if it's a GET request and 'id' parameter is set
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $userData = getUserById($userId, $conn);
    
    if ($userData) {
        echo json_encode($userData);
    } else {
        echo json_encode(array('error' => 'User not found.'));
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if all required parameters are set
    if (isset($_POST['edit-user-id'], $_POST['edit-username'], $_POST['edit-name'], $_POST['edit-email'])) {
        $userId = $_POST['edit-user-id'];
        $username = $_POST['edit-username'];
        $name = $_POST['edit-name'];
        $email = $_POST['edit-email'];
        $password = $_POST['edit-password'];
        $status = isset($_POST['edit-status']) ? $_POST['edit-status'] : '';

        // Update user data including password
        if (updateUserWithPassword($userId, $username, $name, $email, $password, $status, $conn)) {
            echo json_encode(array('success' => 'User data updated successfully.'));

            // Check if the updated user is the currently logged-in user
            if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $userId) {
                // If yes, update session data with the new user data
                $_SESSION['user']['username'] = $username;
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['users_status'] = $status;
            }
        } else {
            echo json_encode(array('error' => 'Failed to update user data.'));
        }
    } else {
        echo json_encode(array('error' => 'Incomplete data provided.'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request.'));
}
?>
