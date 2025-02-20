<?php
include __DIR__ . '/includes/connection.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(substr($_POST['username'], 0, 30));
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->bind_param("si", $newHash, $row['id']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("Location: todo.php");
            exit();
        }
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Todolist UKK 2025</title>

    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="assets/fontawesome/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <form action="index.php" method="POST" class="needs-validation" novalidate>
            <div class="card" style="width: 22rem;">
                <div class="card-header text-start">
                    <strong>LOGIN</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">LOGIN</button>
                    <div class="text-center mt-3">
                        <small>Don't have an account? <a href="auth/registration.php" class="text-primary">Sign Up</a></small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>