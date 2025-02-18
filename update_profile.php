<?php
include "connection.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$fullname = trim($_POST['fullname'] ?? '');
$newUsername = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($fullname) || empty($newUsername)) {
    header("Location: edit_profile.php?update=failed&error=invalid_input");
    exit();
}

if (stripos($fullname, 'iosong') !== false || stripos($newUsername, 'iosong') !== false) {
    header("Location: edit_profile.php?update=failed&error=forbidden_word");
    exit();
}

if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users 
                            SET fullname = ?, 
                                username = ?, 
                                password = ? 
                            WHERE username = ?");
    $stmt->bind_param("ssss", $fullname, $newUsername, $hashedPassword, $username);
} else {
    $stmt = $conn->prepare("UPDATE users 
                            SET fullname = ?, 
                                username = ? 
                            WHERE username = ?");
    $stmt->bind_param("sss", $fullname, $newUsername, $username);
}

$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['username'] = $newUsername;
    header("Location: edit_profile.php?update=success");
} else {
    header("Location: edit_profile.php?update=failed");
}

$stmt->close();
exit();
