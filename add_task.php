<?php
include "connection.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$userQuery = "SELECT id FROM users WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) {
    header("Location: todo.php?task_added=user_not_found");
    exit();
}

$user_id = $userData['id'];

$task = trim($_POST['task'] ?? '');
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? 'Pending';

if ($task === '') {
    header("Location: todo.php?task_added=empty_task");
    exit();
}

if (!DateTime::createFromFormat('Y-m-d', $due_date)) {
    header("Location: todo.php?task_added=invalid_date");
    exit();
}

$sql = "INSERT INTO tasks (user_id, task, due_date, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $user_id, $task, $due_date, $status);

if ($stmt->execute()) {
    header("Location: todo.php?task_added=success");
} else {
    header("Location: todo.php?task_added=db_error");
}

$stmt->close();
$conn->close();
exit();
