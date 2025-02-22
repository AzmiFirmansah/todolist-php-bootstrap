<?php
include __DIR__ . "/../includes/connection.php";
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT fullname, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

$current_password_error = $_SESSION['old']['current_password_error'] ?? '';
$password_error = $_SESSION['old']['password_error'] ?? '';
$username_error = $_SESSION['old']['username_error'] ?? '';
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - UKK Todolist</title>

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
                    <button class="btn btn-transparent border-0 text-primary" type="button" data-bs-toggle="dropdown">
                        <i class="fa fa-user text-primary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger w-50 mx-auto">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success w-50 mx-auto">
                <?= htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="update_profile.php">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($userData['fullname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($userData['username']) ?>" required>
                                <?php if ($username_error) : ?>
                                    <small class="text-danger">
                                        <?= htmlspecialchars($username_error) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" placeholder="Enter your current password" required>
                                <?php if ($current_password_error) : ?>
                                    <small class="text-danger">
                                        <?= htmlspecialchars($current_password_error) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                                <?php if ($password_error) : ?>
                                    <small class="text-danger">
                                        <?= htmlspecialchars($password_error) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
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