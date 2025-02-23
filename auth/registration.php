<?php
include __DIR__ . '/../includes/connection.php';
session_start();

$fullnameError = "";
$usernameError = "";
$passwordError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = htmlspecialchars(trim($_POST['fullname']), ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);

    if (empty($fullname)) {
        $fullnameError = "Full name cannot be empty.";
    } elseif (strlen($fullname) > 30) {
        $fullnameError = "Full name cannot exceed 100 characters.";
    }

    if (empty($username)) {
        $usernameError = "Username cannot be empty.";
    } elseif (strpos($username, ' ') !== false) {
        $usernameError = "Username cannot contain spaces.";
    }

    if (empty($password)) {
        $passwordError = "Password cannot be empty.";
    } elseif (strpos($password, ' ') !== false) {
        $passwordError = "Password cannot contain spaces.";
    } elseif (strlen($password) < 8) {
        $passwordError = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        $passwordError = "Password must contain at least one number and one special character.";
    }

    if (empty($fullnameError) && empty($usernameError) && empty($passwordError)) {
        $lowerUsername = strtolower($username);
        $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(username) = ?");
        $stmt->bind_param("s", $lowerUsername);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $usernameError = "Username is already taken, please choose another.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $username, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['signup_success'] = "Account created successfully!";
                $stmt->close();
                $conn->close();
                header("Location: ../index.php");
                exit();
            } else {
                $usernameError = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Todolist UKK 2025</title>

    <!-- Bootstrap CSS -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <form action="registration.php" method="post" style="width: 22rem;">
            <div class="card">
                <div class="card-header text-start">
                    <strong>SIGN UP</strong>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['signup_success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['signup_success']); ?>
                        </div>
                        <?php unset($_SESSION['signup_success']); ?>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="fullname" placeholder="Enter your full name" required>
                        <?php if (!empty($fullnameError)): ?>
                            <small class="text-danger"><?= htmlspecialchars($fullnameError); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Choose a username" required>
                        <?php if (!empty($usernameError)): ?>
                            <small class="text-danger"><?= htmlspecialchars($usernameError); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Create a password" required>
                        <?php if (!empty($passwordError)): ?>
                            <small class="text-danger"><?= htmlspecialchars($passwordError); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">SIGN UP</button>
                    <div class="text-center mt-3">
                        <small>Already have an account? <a href="../index.php" class="text-primary">Login</a></small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>