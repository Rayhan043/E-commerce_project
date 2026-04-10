<?php


$page_title = "Product Details";
include_once '../config/db.php';
include_once '../includes/functions.php';

$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    redirect('/E-comm/pages/shop.php');
}

$query = "SELECT p.*, c.category_name FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('/E-comm/pages/shop.php');
}

$product = $result->fetch_assoc();
$stmt->close();

$related_query = "SELECT * FROM products WHERE category_id = ? AND product_id != ? AND stock > 0 LIMIT 4";
$related_stmt = $conn->prepare($related_query);
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['product_name']; ?> - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/E-comm/pages/shop.php">Shop</a></li>
                <li class="breadcrumb-item"><a href="/E-comm/pages/shop.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name']; ?></a></li>
                <li class="breadcrumb-item active"><?php echo $product['product_name']; ?></li>
            </ol>
        </nav>

        <div class="row mb-5">
            <div class="col-md-5">
                <div class="mb-3">
                    <?php if ($product['image']): ?>
                        <img src="/E-comm/uploads/products/<?php echo $product['image']; ?>" 
                             class="img-fluid rounded" alt="<?php echo $product['product_name']; ?>">
                    <?php else: ?>
                        <img src="/E-comm/assets/images/no-image.png" 
                             class="img-fluid rounded" alt="No image">
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-7">
                <h1 class="mb-3"><?php echo $product['product_name']; ?></h1>

                <div class="mb-3">
                    <span class="badge bg-primary"><?php echo $product['category_name']; ?></span>
                    <span class="badge bg-info">SKU: <?php echo $product['sku']; ?></span>
                </div>

                <hr>

                <div class="mb-4">
                    <h3 class="text-primary"><?php echo formatPrice($product['price']); ?></h3>
                </div>

                <div class="mb-4">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="badge bg-success fs-6">In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br($product['description']); ?></p>
                </div>

                <?php if (isUserLoggedIn() && $product['stock'] > 0): ?>
                    <div class="mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="col-form-label">Quantity:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" class="form-control" id="quantity" 
                                       value="1" min="1" max="<?php echo $product['stock']; ?>">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product_id; ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php elseif (!isUserLoggedIn()): ?>
                    <div class="mb-4">
                        <a href="/E-comm/pages/login.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login to Add to Cart
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mb-4">
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="fas fa-ban"></i> Out of Stock
                        </button>
                    </div>
                <?php endif; ?>

                <a href="/E-comm/pages/shop.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Shop
                </a>
            </div>
        </div>

        <?php if ($related_result->num_rows > 0): ?>
            <div class="mt-5">
                <h3 class="mb-4">Related Products</h3>
                <div class="row g-4">
                    <?php while ($related = $related_result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="product-image" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                                    <?php if ($related['image']): ?>
                                        <img src="/E-comm/uploads/products/<?php echo $related['image']; ?>" 
                                             class="card-img-top" alt="<?php echo $related['product_name']; ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="/E-comm/assets/images/no-image.png" 
                                             class="card-img-top" alt="No image" 
                                             style="width: 100%; height: 100%; object-fit: contain;">
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $related['product_name']; ?></h5>
                                    <p class="h5 text-primary"><?php echo formatPrice($related['price']); ?></p>
                                    <a href="/E-comm/pages/product-detail.php?id=<?php echo $related['product_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


