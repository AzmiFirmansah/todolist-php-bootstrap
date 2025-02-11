<?php
include "connection.php";

$id = $_POST['id'];
$task = $_POST['task'];
$due_date = $_POST['due_date'];
$status = $_POST['status'];

$sql = "UPDATE tasks
        SET task = '$task', 
            due_date = '$due_date', 
            status = '$status' 
        WHERE id = '$id'";


mysqli_query($conn, $sql);
$r = mysqli_affected_rows($conn);
if ($r > 0) {
    header("Location: index.php?task_updated=true");
} else {
    header("Location: index.php?task_updated=false");
}
