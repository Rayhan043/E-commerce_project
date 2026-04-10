<?php


$page_title = "My Orders";
include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    redirect('/E-comm/pages/login.php');
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-history"></i> My Orders</h1>

        <?php if ($orders_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <?php 
                            $items_query = "SELECT COUNT(*) as count FROM order_items WHERE order_id = ?";
                            $items_stmt = $conn->prepare($items_query);
                            $items_stmt->bind_param("i", $order['order_id']);
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();
                            $items_row = $items_result->fetch_assoc();
                            $items_stmt->close();
                            
                            $status_badge = match($order['status']) {
                                'pending' => '<span class="badge bg-warning">Pending</span>',
                                'processing' => '<span class="badge bg-info">Processing</span>',
                                'shipped' => '<span class="badge bg-primary">Shipped</span>',
                                'delivered' => '<span class="badge bg-success">Delivered</span>',
                                'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                                default => '<span class="badge bg-secondary">Unknown</span>'
                            };
                            ?>
                            <tr>
                                <td><strong><?php echo $order['order_number']; ?></strong></td>
                                <td><?php echo formatDate($order['created_at']); ?></td>
                                <td><?php echo $items_row['count']; ?></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td><?php echo $status_badge; ?></td>
                                <td>
                                    <a href="/E-comm/pages/order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> You haven't placed any orders yet.
                <a href="/E-comm/pages/shop.php" class="alert-link">Start shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


