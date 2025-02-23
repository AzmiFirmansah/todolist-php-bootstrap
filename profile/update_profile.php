<?php
include __DIR__ . "/../includes/connection.php";
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = trim($_POST['fullname'] ?? '');
$new_username = trim($_POST['username'] ?? '');
$current_password = trim($_POST['current_password'] ?? '');
$new_password = trim($_POST['password'] ?? '');

$fullnameError = "";
$usernameError = "";
$currentPasswordError = "";
$newPasswordError = "";

if (empty($fullname)) {
    $fullnameError = "Full name cannot be empty.";
} elseif (strlen($fullname) > 30) {
    $fullnameError = "Full name cannot exceed 100 characters.";
}

if (empty($new_username)) {
    $usernameError = "Username cannot be empty.";
} elseif (preg_match('/\s/', $new_username)) {
    $usernameError = "Username cannot contain spaces.";
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($fullname === $user['fullname'] && $new_username === $user['username'] && empty($new_password)) {
    $_SESSION['error'] = "No changes detected.";
    header("Location: edit_profile.php");
    exit();
}

if (!password_verify($current_password, $user['password'])) {
    $currentPasswordError = "Current password is incorrect.";
}

if ($new_username !== $user['username']) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(?) AND id != ?");
    $stmt->bind_param("si", $new_username, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $usernameError = "Username is already taken.";
    }
    $stmt->close();
}

if (!empty($new_password)) {
    if (password_verify($new_password, $user['password'])) {
        $newPasswordError = "New password cannot be the same as the current password.";
    } elseif (preg_match('/\s/', $new_password)) {
        $newPasswordError = "Password cannot contain spaces.";
    } elseif (strlen($new_password) < 8) {
        $newPasswordError = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[0-9]/', $new_password) || !preg_match('/[\W_]/', $new_password)) {
        $newPasswordError = "Password must contain at least one number and one special character.";
    }
}

if (!empty($fullnameError) || !empty($usernameError) || !empty($currentPasswordError) || !empty($newPasswordError)) {
    $_SESSION['old']['fullname_error'] = $fullnameError;
    $_SESSION['old']['username_error'] = $usernameError;
    $_SESSION['old']['current_password_error'] = $currentPasswordError;
    $_SESSION['old']['password_error'] = $newPasswordError;
    header("Location: edit_profile.php");
    exit();
}

$query = "UPDATE users SET fullname = ?, username = ?";
$params = [$fullname, $new_username];
$types = "ss";

if (!empty($new_password)) {
    $query .= ", password = ?";
    $types .= "s";
    $params[] = password_hash($new_password, PASSWORD_BCRYPT);
}

$query .= " WHERE id = ?";
$types .= "i";
$params[] = $user_id;

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully.";
    $_SESSION['username'] = $new_username;
} else {
    $_SESSION['error'] = "Error updating profile.";
}

$stmt->close();
header("Location: edit_profile.php");
exit();
