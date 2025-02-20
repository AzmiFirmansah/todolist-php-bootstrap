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

if (empty($fullname) || empty($new_username)) {
    $_SESSION['error'] = "All fields are required";
    header("Location: edit_profile.php");
    exit();
}

$stmt = $conn->prepare("SELECT fullname, username, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($fullname === $user['fullname'] && $new_username === $user['username'] && empty($new_password)) {
    $_SESSION['error'] = "No changes detected";
    header("Location: edit_profile.php");
    exit();
}

if (!password_verify($current_password, $user['password'])) {
    $_SESSION['old']['current_password_error'] = "Current password is incorrect";
    header("Location: edit_profile.php");
    exit();
}

if ($new_username !== $user['username']) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Username already taken";
        header("Location: edit_profile.php");
        exit();
    }
    $stmt->close();
}

$query = "UPDATE users SET fullname = ?, username = ?";
$params = [$fullname, $new_username];
$types = "ss";

if (!empty($new_password)) {
    if (strpos($new_password, ' ') !== false) {
        $_SESSION['old']['password_error'] = "Password cannot contain spaces";
        header("Location: edit_profile.php");
        exit();
    }

    if (strlen($new_password) < 8) {
        $_SESSION['old']['password_error'] = "Password must be at least 8 characters";
        header("Location: edit_profile.php");
        exit();
    }

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
    $_SESSION['success'] = "Profile updated successfully";
    $_SESSION['username'] = $new_username;
} else {
    $_SESSION['error'] = "Error updating profile: " . $conn->error;
}

$stmt->close();
header("Location: edit_profile.php");
exit();
