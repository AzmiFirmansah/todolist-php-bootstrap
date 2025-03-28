<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/connection.php";

$username = $_SESSION['username'];
$user_id = null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $userData = $result->fetch_assoc();
    $user_id = (int)$userData['id'];
} else {
    $_SESSION['task_deleted'] = 'user_not_found';
    header("Location: ../todo.php");
    exit();
}

if ($id > 0 && $user_id !== null) {
    $checkStmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 1) {
        $deleteStmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $deleteStmt->bind_param("ii", $id, $user_id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows === 1) {
            $_SESSION['task_deleted'] = 'success';
        } else {
            $_SESSION['task_deleted'] = 'db_error';
        }
        $deleteStmt->close();
    } else {
        $_SESSION['task_deleted'] = 'unauthorized';
    }
    $checkStmt->close();
} else {
    $_SESSION['task_deleted'] = 'invalid_id';
}

$stmt->close();
$conn->close();
header("Location: ../todo.php");
exit();
