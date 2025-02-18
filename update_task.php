<?php
include "connection.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$task = trim($_POST['task'] ?? '');
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? 'Pending';
$user_id = $_SESSION['user_id'];

if (!$id || empty($task) || empty($due_date)) {
    header("Location: todo.php?task_updated=false&error=invalid_input");
    exit();
}

if (stripos($task, 'iosong') !== false) {
    header("Location: todo.php?task_updated=false&error=forbidden_word");
    exit();
}

$stmt = $conn->prepare("UPDATE tasks 
                        SET task = ?, 
                            due_date = ?, 
                            status = ? 
                        WHERE id = ? AND user_id = ?");
$stmt->bind_param("sssii", $task, $due_date, $status, $id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: todo.php?task_updated=success");
} else {
    header("Location: todo.php?task_updated=failed");
}

$stmt->close();
exit();
