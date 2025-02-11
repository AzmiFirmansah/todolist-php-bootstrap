<?php
include "connection.php";

$sql = "DELETE FROM tasks WHERE id = '$_GET[id]'";
mysqli_query($conn, $sql);

$r = mysqli_affected_rows($conn);
if ($r > 0) {
    header("Location: index.php?task_deleted=true");
} else {
    header("Location: index.php?task_deleted=false");
}
