<?php


$page_title = "Register";
include_once '../config/db.php';
include_once '../includes/functions.php';

if (isUserLoggedIn()) {
    redirect('/E-comm/pages/shop.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');

    $errors = [];

    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long";
    }

    if (empty($email) || !validateEmail($email)) {
        $errors[] = "Please enter a valid email address";
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (empty($first_name) || empty($last_name)) {
        $errors[] = "Please enter your first and last name";
    }

    if (empty($errors)) {
        $check_query = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = "Username or email already exists";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $insert_query = "INSERT INTO users (username, email, password, first_name, last_name) 
                         VALUES (?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sssss", $username, $email, $hashed_password, $first_name, $last_name);

        if ($insert_stmt->execute()) {
            showSuccess("Registration successful! Please log in.");
            redirect('/E-comm/pages/login.php');
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $insert_stmt->close();
    }

    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 col-lg-5 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">
                            <i class="fas fa-user-plus"></i> Create Account
                        </h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo isset($first_name) ? $first_name : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo isset($last_name) ? $last_name : ''; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($username) ? $username : ''; ?>" required 
                                       minlength="3">
                                <small class="form-text text-muted">Minimum 3 characters</small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <small class="form-text text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-user-check"></i> Register
                            </button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center text-muted">
                            Already have an account? 
                            <a href="/E-comm/pages/login.php" class="text-decoration-none">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


