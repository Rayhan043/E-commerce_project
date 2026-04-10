<?php


$page_title = "Checkout";
include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    redirect('/E-comm/pages/login.php');
}

$user_id = $_SESSION['user_id'];

$query = "SELECT c.product_id, c.quantity, p.price FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}

$stmt->close();

if (empty($cart_items)) {
    showError("Your cart is empty");
    redirect('/E-comm/pages/cart.php');
}

$user_query = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

$shipping = 10;
$tax = round($subtotal * 0.10, 2);
$total = $subtotal + $shipping + $tax;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitize($_POST['shipping_address'] ?? '');
    $shipping_city = sanitize($_POST['shipping_city'] ?? '');
    $shipping_state = sanitize($_POST['shipping_state'] ?? '');
    $shipping_postal = sanitize($_POST['shipping_postal_code'] ?? '');
    $shipping_country = sanitize($_POST['shipping_country'] ?? '');

    $errors = [];
    if (empty($shipping_address)) $errors[] = "Shipping address is required";
    if (empty($shipping_city)) $errors[] = "City is required";
    if (empty($shipping_state)) $errors[] = "State is required";
    if (empty($shipping_postal)) $errors[] = "Postal code is required";
    if (empty($shipping_country)) $errors[] = "Country is required";

    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            $order_number = generateOrderNumber();
            $order_query = "INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address, 
                           shipping_city, shipping_state, shipping_postal_code, shipping_country) 
                           VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)";

            $order_stmt = $conn->prepare($order_query);
            $order_stmt->bind_param("isdsssss", $user_id, $order_number, $total, $shipping_address, 
                                   $shipping_city, $shipping_state, $shipping_postal, $shipping_country);
            $order_stmt->execute();
            $order_id = $conn->insert_id;
            $order_stmt->close();

            foreach ($cart_items as $item) {
                $item_total = $item['price'] * $item['quantity'];
                $item_query = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) 
                              VALUES (?, ?, ?, ?, ?)";

                $item_stmt = $conn->prepare($item_query);
                $item_stmt->bind_param("iiidd", $order_id, $item['product_id'], $item['quantity'], 
                                      $item['price'], $item_total);
                $item_stmt->execute();
                $item_stmt->close();

                $stock_query = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                $stock_stmt = $conn->prepare($stock_query);
                $stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stock_stmt->execute();
                $stock_stmt->close();
            }

            $clear_query = "DELETE FROM cart WHERE user_id = ?";
            $clear_stmt = $conn->prepare($clear_query);
            $clear_stmt->bind_param("i", $user_id);
            $clear_stmt->execute();
            $clear_stmt->close();

            $conn->commit();

            showSuccess("Order placed successfully! Order ID: " . $order_number);
            redirect('/E-comm/pages/orders.php');

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Order processing failed. Please try again.";
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-credit-card"></i> Checkout</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Address *</label>
                                <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                       value="<?php echo $user['address'] ?? ''; ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shipping_city" class="form-label">City *</label>
                                        <input type="text" class="form-control" id="shipping_city" name="shipping_city" 
                                               value="<?php echo $user['city'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shipping_state" class="form-label">State *</label>
                                        <input type="text" class="form-control" id="shipping_state" name="shipping_state" 
                                               value="<?php echo $user['state'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shipping_postal_code" class="form-label">Postal Code *</label>
                                        <input type="text" class="form-control" id="shipping_postal_code" name="shipping_postal_code" 
                                               value="<?php echo $user['postal_code'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shipping_country" class="form-label">Country *</label>
                                        <input type="text" class="form-control" id="shipping_country" name="shipping_country" 
                                               value="<?php echo $user['country'] ?? 'USA'; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check"></i> Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <small class="text-muted">
                                        Product x<?php echo $item['quantity']; ?>
                                    </small>
                                    <small><strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong></small>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span><?php echo formatPrice($shipping); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax (10%):</span>
                                <span><?php echo formatPrice($tax); ?></span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-success h5"><?php echo formatPrice($total); ?></strong>
                        </div>

                        <div class="alert alert-warning mt-3" role="alert">
                            <small><i class="fas fa-info-circle"></i> This is a simulated checkout. No real payment processing occurs.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


