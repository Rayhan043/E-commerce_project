<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    
    $allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $allowed_statuses)) {
        $update = "UPDATE orders SET status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
}

$filter = isset($_GET['status']) && !empty($_GET['status']) ? "WHERE status = '" . $conn->real_escape_string($_GET['status']) . "'" : "";

$result = $conn->query("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o 
                       JOIN users u ON o.user_id = u.user_id 
                       {$filter}
                       ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/E-comm/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="/E-comm/admin/orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Manage Orders</h1>

        <div class="mb-4">
            <a href="/E-comm/admin/orders.php" class="btn btn-secondary <?php echo !isset($_GET['status']) ? 'active' : ''; ?>">All</a>
            <a href="?status=pending" class="btn btn-warning <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="?status=processing" class="btn btn-info <?php echo isset($_GET['status']) && $_GET['status'] == 'processing' ? 'active' : ''; ?>">Processing</a>
            <a href="?status=shipped" class="btn btn-primary <?php echo isset($_GET['status']) && $_GET['status'] == 'shipped' ? 'active' : ''; ?>">Shipped</a>
            <a href="?status=delivered" class="btn btn-success <?php echo isset($_GET['status']) && $_GET['status'] == 'delivered' ? 'active' : ''; ?>">Delivered</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $order['order_number']; ?></strong></td>
                                    <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit();">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo formatDate($order['created_at']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['order_id']; ?>">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="orderModal<?php echo $order['order_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order <?php echo $order['order_number']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Customer:</strong> <?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
                                                <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                                <p><strong>Address:</strong> <?php echo $order['shipping_address']; ?>, <?php echo $order['shipping_city']; ?></p>
                                                <p><strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                                                <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


