<?php


$page_title = "My Profile";
include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    redirect('/E-comm/pages/login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $postal_code = sanitize($_POST['postal_code'] ?? '');
    $country = sanitize($_POST['country'] ?? '');

    if (empty($first_name) || empty($last_name)) {
        $error = "First name and last name are required";
    } else {
        $update_query = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ?, 
                        city = ?, state = ?, postal_code = ?, country = ? WHERE user_id = ?";
        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssssssi", $first_name, $last_name, $phone, $address, 
                                $city, $state, $postal_code, $country, $user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['first_name'] = $first_name;
            showSuccess("Profile updated successfully");
            redirect('/E-comm/pages/profile.php');
        } else {
            $error = "Failed to update profile. Please try again.";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-user-circle"></i> My Profile</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success = getSuccessMessage()): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Edit Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo $user['first_name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo $user['last_name']; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                                <small class="form-text text-muted">Email cannot be changed</small>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo $user['phone'] ?? ''; ?>">
                            </div>

                            <hr>

                            <h5 class="mb-3">Shipping Address</h5>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo $user['address'] ?? ''; ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo $user['city'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="<?php echo $user['state'] ?? ''; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                           value="<?php echo $user['postal_code'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="<?php echo $user['country'] ?? ''; ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>

                            <a href="/E-comm/pages/shop.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Username:</strong><br>
                            <?php echo $user['username']; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Email:</strong><br>
                            <?php echo $user['email']; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Member Since:</strong><br>
                            <?php echo formatDate($user['created_at']); ?>
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="/E-comm/pages/orders.php" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-history"></i> View Orders
                        </a>
                        <a href="/E-comm/pages/cart.php" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-shopping-cart"></i> View Cart
                        </a>
                        <a href="/E-comm/pages/logout.php" class="btn btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


