<?php
include "connection.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    echo "User not found.";
    exit();
}

$usernameError = "";
$passwordError = "";
$noChangesError = "";

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $newUsername = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate username
    if (empty($newUsername)) {
        $usernameError = "Username cannot be empty.";
    } elseif (strpos($newUsername, ' ') !== false) {
        $usernameError = "Username cannot contain spaces.";
    }

    // Validate password
    if (!empty($password)) {
        if (strpos($password, ' ') !== false) {
            $passwordError = "Password cannot have spaces.";
        } elseif (strlen($password) < 8) {
            $passwordError = "Password must be at least 8 characters.";
        }
    }

    // Check if there are changes
    if (empty($usernameError) && empty($passwordError)) {
        $changesMade = false;

        // Check if the full name or username has changed
        if ($fullname !== $userData['fullname']) {
            $changesMade = true;
        }
        if ($newUsername !== $userData['username']) {
            $changesMade = true;
        }
        if (!empty($password)) {
            $changesMade = true;
        }

        // If no changes were made
        if (!$changesMade) {
            $noChangesError = "No changes were made to your profile.";
        }

        // Proceed to update if there are changes
        if ($changesMade) {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $fullname, $newUsername, $hashedPassword, $userData['id']);
            } else {
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ? WHERE id = ?");
                $stmt->bind_param("ssi", $fullname, $newUsername, $userData['id']);
            }

            if ($stmt->execute()) {
                $_SESSION['success'] = "Profile updated successfully.";
                $_SESSION['username'] = $newUsername; // Update session username
            } else {
                $_SESSION['error'] = "Error updating profile.";
            }

            $stmt->close();
            header("Location: edit_profile.php");
            exit();
        }
    }
}
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
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="todo.php">TodoList App</a>
            <div class="d-flex ms-auto align-items-center">
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

    <div class="container mt-5">
        <!-- Alert Section -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger w-50 mx-auto"><?= $_SESSION['error'];
                                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success w-50 mx-auto"><?= $_SESSION['success'];
                                                            unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($noChangesError)): ?>
            <div class="alert alert-warning w-50 mx-auto"><?= $noChangesError; ?></div>
        <?php endif; ?>

        <!-- Profile Edit Form -->
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    value="<?= htmlspecialchars($userData['fullname']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= htmlspecialchars($userData['username']); ?>" required>
                                <?php if (!empty($usernameError)): ?>
                                    <small class="text-danger"><?= $usernameError; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Leave blank to keep current password">
                                <?php if (!empty($passwordError)): ?>
                                    <small class="text-danger"><?= $passwordError; ?></small>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="todo.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>