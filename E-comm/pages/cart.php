<?php


$page_title = "Shopping Cart";
include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    redirect('/E-comm/pages/login.php');
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $delete_query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $user_id, $product_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    showSuccess("Product removed from cart");
    redirect('/E-comm/pages/cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['quantity'] ?? [] as $product_id => $quantity) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            $delete_query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("ii", $user_id, $product_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        } else {
            $stock_query = "SELECT stock FROM products WHERE product_id = ?";
            $stock_stmt = $conn->prepare($stock_query);
            $stock_stmt->bind_param("i", $product_id);
            $stock_stmt->execute();
            $stock_result = $stock_stmt->get_result();
            $stock_row = $stock_result->fetch_assoc();
            $stock_stmt->close();

            if ($quantity > $stock_row['stock']) {
                showError("Quantity exceeds available stock for some items");
                redirect('/E-comm/pages/cart.php');
            }

            $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }

    showSuccess("Cart updated successfully");
    redirect('/E-comm/pages/cart.php');
}

$query = "SELECT c.cart_id, c.product_id, c.quantity, p.product_name, p.price, p.stock, p.image 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.user_id = ?
          ORDER BY c.added_at DESC";

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

$shipping = 10;
$tax = round($subtotal * 0.10, 2);
$total = $subtotal + $shipping + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>

        <?php if (count($cart_items) > 0): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cart_items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if ($item['image']): ?>
                                                                <img src="/E-comm/uploads/products/<?php echo $item['image']; ?>" 
                                                                     alt="<?php echo $item['product_name']; ?>" 
                                                                     style="width: 60px; height: 60px; object-fit: cover; margin-right: 10px;">
                                                            <?php else: ?>
                                                                <img src="/E-comm/assets/images/no-image.png" 
                                                                     alt="No image" 
                                                                     style="width: 60px; height: 60px; object-fit: contain; margin-right: 10px;">
                                                            <?php endif; ?>
                                                            <a href="/E-comm/pages/product-detail.php?id=<?php echo $item['product_id']; ?>">
                                                                <?php echo $item['product_name']; ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td><?php echo formatPrice($item['price']); ?></td>
                                                    <td>
                                                        <input type="number" class="form-control" name="quantity[<?php echo $item['product_id']; ?>]" 
                                                               value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['stock']; ?>" 
                                                               style="width: 70px;">
                                                    </td>
                                                    <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                                    <td>
                                                        <a href="?remove=<?php echo $item['product_id']; ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Remove from cart?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sync"></i> Update Cart
                                    </button>
                                    <a href="/E-comm/pages/shop.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
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
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total:</strong>
                                <strong><?php echo formatPrice($total); ?></strong>
                            </div>
                            <a href="/E-comm/pages/checkout.php" class="btn btn-success w-100">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> Your cart is empty.
                <a href="/E-comm/pages/shop.php" class="alert-link">Continue shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


