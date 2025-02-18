<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$showWelcome = !isset($_SESSION['welcome_closed']);

if (isset($_GET['close_welcome'])) {
    $_SESSION['welcome_closed'] = true;
    header("Location: todo.php");
    exit();
}

$userQuery = "SELECT id, fullname FROM users WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

$user_id = $userData['id'];
$fullname = $userData['fullname'];

$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;
$search = htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8');
$status = $_GET['status'] ?? '';

$query = "SELECT * FROM tasks WHERE user_id = ? AND task LIKE ?";
$totalQuery = "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND task LIKE ?";

if (!empty($status)) {
    $query .= " AND status = ?";
    $totalQuery .= " AND status = ?";
}

$query .= " LIMIT ?, ?";

$stmt = $conn->prepare($query);
$params = ["is", $user_id, "%$search%"];

if (!empty($status)) {
    $params[0] .= "s";
    $params[] = $status;
}

$params[0] .= "ii";
$params[] = $start;
$params[] = $limit;

$stmt->bind_param(...$params);
$stmt->execute();
$result = $stmt->get_result();

$totalStmt = $conn->prepare($totalQuery);
$totalParams = ["is", $user_id, "%$search%"];

if (!empty($status)) {
    $totalParams[0] .= "s";
    $totalParams[] = $status;
}

$totalStmt->bind_param(...$totalParams);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$total = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todolist UKK 2025</title>

    <!-- bootstrap CSS -->
    <link href="bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
</head>

<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="todo.php">TodoList App</a>
            <div class="d-flex ms-auto align-items-center">
                <form action="todo.php" method="get" class="d-flex me-3" role="search">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search tasks" aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
                <div class="dropdown">
                    <button class="btn btn-transparent border-0 text-primary" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-user text-primary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <?php if ($showWelcome): ?>
        <div class="alert bg-white text-dark alert-dismissible fade show shadow-sm d-flex align-items-center p-3 rounded" role="alert">
            <span class="fw-bold me-1">Welcome, <?php echo $fullname; ?>!</span> to the To-Do List App
            <a href="?close_welcome=1" class="btn-close ms-auto" aria-label="Close"></a>
        </div>
    <?php endif; ?>

    <!-- Filter Status -->
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-3">
                <form method="get" action="todo.php">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <label for="filterStatus" class="form-label">Filter by Status:</label>
                    <select name="status" id="filterStatus" class="form-select w-100" onchange="this.form.submit()">
                        <option value="" <?php echo empty($status) ? 'selected' : ''; ?>>All</option>
                        <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Completed" <?php echo $status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </form>

            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container mt-3">
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Task List</h5>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="fa-solid fa-plus"></i> Add Task
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- alert -->
                        <?php
                        if (isset($_GET['task_added'])) {
                            $message = match ($_GET['task_added']) {
                                'success' => 'Task added successfully!',
                                'empty_task' => 'Task cannot be empty!',
                                'invalid_date' => 'Invalid date format!',
                                'db_error' => 'Database error occurred!',
                                default => 'Task addition failed!',
                            };
                            echo '
                            <div class="alert alert-' . ($_GET['task_added'] === 'success' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">
                                ' . htmlspecialchars($message) . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                        }

                        if (isset($_GET['task_updated'])) {
                            $message = match ($_GET['task_updated']) {
                                'success' => 'Task updated successfully!',
                                'failed' => 'Task update failed!',
                                default => 'Unknown update status!',
                            };
                            echo '
                            <div class="alert alert-' . ($_GET['task_updated'] === 'success' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">
                                ' . htmlspecialchars($message) . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                        }

                        if (isset($_GET['task_deleted'])) {
                            $message = match ($_GET['task_deleted']) {
                                'success' => 'Task deleted successfully!',
                                'unauthorized' => 'You are not authorized to delete this task!',
                                'invalid_id' => 'Invalid task ID!',
                                'db_error' => 'Failed to delete task due to database error!',
                                default => 'Task deletion failed!',
                            };
                            echo '
                            <div class="alert alert-' . ($_GET['task_deleted'] === 'success' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">
                                ' . htmlspecialchars($message) . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                        }
                        ?>

                        <table class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Task</td>
                                    <td>Due Date</td>
                                    <td>Status</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = $start + 1;
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_array($result)) {
                                        $taskContent = $row['status'] === 'Completed'
                                            ? "<s>{$row['task']}</s>"
                                            : $row['task'];
                                        echo "<tr>
                                            <td>" . $no . "</td>
                                            <td>" . $taskContent . "</td>
                                            <td>" . $row['due_date'] . "</td>
                                            <td>" . $row['status'] . "</td>
                                            <td>
                                                <a href='edit_task.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>
                                                    <i class='fa-solid fa-pen'></i> Edit
                                                </a>
                                               <a href='delete_task.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>
                                                    <i class='fa-solid fa-trash'></i> Delete
                                                </a>
                                            </td>
                                        </tr>";
                                        $no++;
                                    }
                                } else {
                                    echo "<tr>
                                        <td colspan='5' class='text-center'>No tasks available</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total > $limit): ?>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-end me-3">
                                <li class="page-item <?php echo $page == 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page == $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addTaskModalLabel">Add Task</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_task.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="taskName" name="task" placeholder="Enter your task here" required>
                        </div>
                        <div class="mb-3">
                            <label for="dueDate" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="dueDate" name="due_date" required min="1900-01-01" max="2099-12-31">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>