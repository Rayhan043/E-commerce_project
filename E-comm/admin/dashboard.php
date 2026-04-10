<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];

$revenue_result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'");
$revenue_row = $revenue_result->fetch_assoc();
$total_revenue = $revenue_row['total'] ?? 0;

$recent_orders = $conn->query("SELECT o.*, u.first_name, u.last_name FROM orders o 
                               JOIN users u ON o.user_id = u.user_id 
                               ORDER BY o.created_at DESC 
                               LIMIT 5");

$low_stock = $conn->query("SELECT product_id, product_name, stock FROM products 
                          WHERE stock <= 5 
                          ORDER BY stock ASC 
                          LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/admin-style.css" rel="stylesheet">
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['admin_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/E-comm/admin/contact-messages.php">Messages</a></li>
                            <li><a class="dropdown-item" href="/E-comm/">View Store</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/E-comm/admin/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-0"><i class="fas fa-chart-line"></i> Dashboard</h1>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="mb-0"><?php echo $total_users; ?></h2>
                        <small><i class="fas fa-users"></i> Registered users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <h2 class="mb-0"><?php echo $total_products; ?></h2>
                        <small><i class="fas fa-boxes"></i> In catalog</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h2 class="mb-0"><?php echo $total_orders; ?></h2>
                        <small><i class="fas fa-shopping-cart"></i> All time</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2 class="mb-0"><?php echo formatPrice($total_revenue); ?></h2>
                        <small><i class="fas fa-dollar-sign"></i> Delivered orders</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-boxes fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Manage Products</h5>
                        <p class="card-text text-muted">Add, edit, delete products</p>
                        <a href="/E-comm/admin/products.php" class="btn btn-primary">
                            Manage <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-th fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Manage Categories</h5>
                        <p class="card-text text-muted">Add, edit, delete categories</p>
                        <a href="/E-comm/admin/categories.php" class="btn btn-primary">
                            Manage <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text text-muted">View and manage users</p>
                        <a href="/E-comm/admin/users.php" class="btn btn-primary">
                            Manage <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Manage Orders</h5>
                        <p class="card-text text-muted">View and update orders</p>
                        <a href="/E-comm/admin/orders.php" class="btn btn-primary">
                            Manage <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $order['order_number']; ?></td>
                                            <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo match($order['status']) {
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                }; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($order['created_at']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="/E-comm/admin/orders.php" class="btn btn-sm btn-outline-primary">
                            View All Orders <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Items</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($low_stock->num_rows > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php while ($item = $low_stock->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0"><?php echo $item['product_name']; ?></p>
                                            <small class="text-muted">Stock: <?php echo $item['stock']; ?></small>
                                        </div>
                                        <span class="badge bg-danger"><?php echo $item['stock']; ?></span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">All products have sufficient stock</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


