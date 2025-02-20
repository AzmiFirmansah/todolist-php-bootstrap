<?php
include __DIR__ . "/../includes/connection.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];

$userQuery = "SELECT id FROM users WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
$userStmt->close();

if (!$userData) {
    header("Location: ../todo.php?task_added=user_not_found");
    exit();
}

$user_id = $userData['id'];

$task = isset($_POST['task']) ? trim($_POST['task']) : '';
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? 'Pending';
$priority = $_POST['priority'] ?? 'Medium';

if ($task === '') {
    header("Location: ../todo.php?task_added=empty_task");
    exit();
}

$dateObject = DateTime::createFromFormat('Y-m-d', $due_date);
if (!$dateObject || $dateObject->format('Y-m-d') !== $due_date) {
    header("Location: ../todo.php?task_added=invalid_date");
    exit();
}

$valid_priorities = ['Low', 'Medium', 'High'];
if (!in_array($priority, $valid_priorities)) {
    header("Location: ../todo.php?task_added=invalid_priority");
    exit();
}

$valid_statuses = ['Pending', 'Completed'];
if (!in_array($status, $valid_statuses)) {
    header("Location: ../todo.php?task_added=invalid_status");
    exit();
}

$sql = "INSERT INTO tasks (user_id, task, due_date, status, priority) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $user_id, $task, $due_date, $status, $priority);

if ($stmt->execute()) {
    header("Location: ../todo.php?task_added=success");
} else {
    header("Location: ../todo.php?task_added=db_error");
}

$stmt->close();
$conn->close();
exit();
