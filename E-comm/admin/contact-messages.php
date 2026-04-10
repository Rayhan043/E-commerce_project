<?php
include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

$create_contact_table = "CREATE TABLE IF NOT EXISTS `contact_messages` (
    `message_id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(200),
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$conn->query($create_contact_table);

$page_title = "Contact Messages";
$messages_result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/E-comm/admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/categories.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/E-comm/admin/contact-messages.php">Messages</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['admin_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/E-comm/admin/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-envelope"></i> Contact Messages</h2>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if ($messages_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($msg = $messages_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $msg['message_id']; ?></td>
                                        <td><?php echo $msg['name']; ?></td>
                                        <td><?php echo $msg['email']; ?></td>
                                        <td><?php echo $msg['subject']; ?></td>
                                        <td><?php echo substr($msg['message'], 0, 100) . (strlen($msg['message']) > 100 ? '...' : ''); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No contact messages yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>