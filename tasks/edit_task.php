<?php
require_once __DIR__ . "/../includes/connection.php";
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = 'unauthorized_access';
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    $_SESSION['error'] = 'user_not_found';
    header("Location: ../todo.php");
    exit();
}

$user_id = $userData['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'invalid_task_id';
    header("Location: ../todo.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    $_SESSION['error'] = 'task_not_found';
    header("Location: ../todo.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Todolist UKK 2025</title>

    <!-- Bootstrap CSS -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="../todo.php">TodoList App</a>
            <div class="d-flex ms-auto align-items-center">
                <div class="dropdown">
                    <button class="btn btn-transparent border-0 text-primary" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-user text-primary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="../profile/edit_profile.php">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Task</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="update_task.php">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <div class="mb-3">
                                <label for="task" class="form-label">Task Name</label>
                                <input type="text" class="form-control" id="task" name="task" value="<?php echo htmlspecialchars($row['task']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($row['due_date']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <?php
                                    $priorities = ['High', 'Medium', 'Low'];
                                    foreach ($priorities as $priority) {
                                        $selected = ($row['priority'] == $priority) ? 'selected' : '';
                                        echo "<option value=\"$priority\" $selected>$priority</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php
                                    $statuses = ['Pending', 'In Progress', 'Completed'];
                                    foreach ($statuses as $status) {
                                        $selected = (strtolower($row['status']) === strtolower($status)) ? 'selected' : '';
                                        echo "<option value=\"$status\" $selected>$status</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Task</button>
                            <a href="../todo.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>