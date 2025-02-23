<?php
require_once __DIR__ . "/../includes/connection.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$task = trim($_POST['task'] ?? '');
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? 'Pending';
$priority = $_POST['priority'] ?? 'Medium';
$user_id = $_SESSION['user_id'];

$allowed_statuses = ['Pending', 'In Progress', 'Completed'];

if (!$id || empty($task) || empty($due_date)) {
    $_SESSION['task_updated'] = 'invalid_input';
    header("Location: ../todo.php");
    exit();
}

if (!in_array($status, $allowed_statuses)) {
    $_SESSION['task_updated'] = 'invalid_status';
    header("Location: ../todo.php");
    exit();
}

$allowed_priorities = ['High', 'Medium', 'Low'];
if (!in_array($priority, $allowed_priorities)) {
    $_SESSION['task_updated'] = 'invalid_priority';
    header("Location: ../todo.php");
    exit();
}

if (stripos($task, 'iosong') !== false) {
    $_SESSION['task_updated'] = 'forbidden_word';
    header("Location: ../todo.php");
    exit();
}

$stmt = $conn->prepare("UPDATE tasks 
                        SET task = ?, 
                            due_date = ?, 
                            status = ?, 
                            priority = ? 
                        WHERE id = ? AND user_id = ?");
$stmt->bind_param("ssssii", $task, $due_date, $status, $priority, $id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['task_updated'] = 'success';
} else {
    $_SESSION['task_updated'] = 'failed';
}

$stmt->close();
header("Location: ../todo.php");
exit();
