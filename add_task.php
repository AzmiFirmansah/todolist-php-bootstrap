<?php
include "connection.php";

$task = $_POST['task'];
$due_date = $_POST['due_date'];
$status = $_POST['status'];

$sql = "INSERT INTO tasks (task, due_date, status) VALUES ('$task', '$due_date', '$status')";

mysqli_query($conn, $sql);

$r = mysqli_affected_rows($conn);
if ($r > 0) {
    header("Location: index.php?task_added=true");
} else {
    header("Location: index.php?task_added=false");
}
