<?php


$page_title = "Order Details";
include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    redirect('/E-comm/pages/login.php');
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    redirect('/E-comm/pages/orders.php');
}

$query = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('/E-comm/pages/orders.php');
}

$order = $result->fetch_assoc();
$stmt->close();

$items_query = "SELECT oi.*, p.product_name, p.image FROM order_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order <?php echo $order['order_number']; ?> - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="mb-4">
            <a href="/E-comm/pages/orders.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-0">Order <?php echo $order['order_number']; ?></h4>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="mb-0"><?php echo $status_badge; ?></h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <h6 class="text-muted">Order Date</h6>
                        <p><strong><?php echo formatDateTime($order['created_at']); ?></strong></p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Total Amount</h6>
                        <p><strong><?php echo formatPrice($order['total_amount']); ?></strong></p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Items</h6>
                        <p><strong><?php 
                        $count_query = "SELECT COUNT(*) as count FROM order_items WHERE order_id = ?";
                        $count_stmt = $conn->prepare($count_query);
                        $count_stmt->bind_param("i", $order_id);
                        $count_stmt->execute();
                        $count_result = $count_stmt->get_result();
                        $count_row = $count_result->fetch_assoc();
                        $count_stmt->close();
                        echo $count_row['count'];
                        ?></strong></p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Last Update</h6>
                        <p><strong><?php echo formatDateTime($order['updated_at']); ?></strong></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = $items_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['image']): ?>
                                                        <img src="/E-comm/uploads/products/<?php echo $item['image']; ?>" 
                                                             alt="<?php echo $item['product_name']; ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                    <?php endif; ?>
                                                    <?php echo $item['product_name']; ?>
                                                </div>
                                            </td>
                                            <td><?php echo formatPrice($item['unit_price']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo formatPrice($item['total_price']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><?php echo $order['shipping_address']; ?></p>
                        <p class="mb-1"><?php echo $order['shipping_city']; ?>, <?php echo $order['shipping_state']; ?> <?php echo $order['shipping_postal_code']; ?></p>
                        <p><?php echo $order['shipping_country']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


