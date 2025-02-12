<?php
include "connection.php";

$totalQuery = "SELECT COUNT(*) AS total FROM tasks";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$total = $totalRow['total'];

$limit = 5;
$totalPages = ceil($total / $limit);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT * FROM tasks WHERE task LIKE '%$search%' LIMIT $start, $limit";
} else {
    $sql = "SELECT * FROM tasks LIMIT $start, $limit";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todolist UKK 2025</title>

    <!-- bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>

<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">TodoList App</a>
            <form action="index.php" method="post" class="d-flex" role="search">
                <input class="form-control me-2" type="search" name="search" placeholder="Search tasks" aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-5">
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
                            echo '
                          <div class="alert alert-' . ($_GET['task_added'] == 'true' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">
                              ' . ($_GET['task_added'] == 'true' ? 'Task added successfully!' : 'Task addition failed!') . '
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                        }

                        if (isset($_GET['task_updated'])) {
                            echo '
                            <div class="alert alert-' . ($_GET['task_updated'] == 'true' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">
                                ' . ($_GET['task_updated'] == 'true' ? 'Task updated successfully!' : 'Task update failed!') . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                        }

                        if (isset($_GET['task_deleted'])) {
                            echo '
                            <div class="alert alert-' . ($_GET['task_deleted'] == 'true' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">
                                ' . ($_GET['task_deleted'] == 'true' ? 'Task deleted successfully!' : 'Task deletion failed!') . '
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
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page == $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
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
                            <input type="date" class="form-control" id="dueDate" name="due_date" required>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>