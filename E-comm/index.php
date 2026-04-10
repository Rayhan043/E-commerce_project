<?php
$page_title = "Home";
include_once 'config/db.php';
include_once 'includes/functions.php';

$featured_query = "SELECT p.*, c.category_name FROM products p 
                   JOIN categories c ON p.category_id = c.category_id 
                   WHERE p.stock > 0
                   ORDER BY RAND()
                   LIMIT 6";

$featured_result = $conn->query($featured_query);

$categories_query = "SELECT * FROM categories LIMIT 5";
$categories_result = $conn->query($categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once 'includes/navbar.php'; ?>

    <div class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">Welcome to ECommerce Store</h1>
                    <p class="lead mb-4">Discover a wide variety of products at unbeatable prices. Shop now and enjoy quality merchandise delivered to your doorstep.</p>
                    <a href="/E-comm/pages/shop.php" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Start Shopping
                    </a>
                </div>
                <div class="col-md-6 text-center">
                    <i class="fas fa-shopping-cart fa-10x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="mb-4 text-center"><i class="fas fa-th"></i> Shop by Category</h2>
        <div class="row g-4 mb-5">
            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo $cat['category_name']; ?></h5>
                            <p class="card-text text-muted"><?php echo $cat['description']; ?></p>
                            <a href="/E-comm/pages/shop.php?category=<?php echo $cat['category_id']; ?>" 
                               class="btn btn-primary">
                                Browse <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <h2 class="mb-4 text-center"><i class="fas fa-star"></i> Featured Products</h2>
        <div class="row g-4 mb-5">
            <?php while ($product = $featured_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm product-card">
                        <div class="product-image" style="height: 250px; overflow: hidden; background: #f8f9fa;">
                            <?php if ($product['image']): ?>
                                <img src="/E-comm/uploads/products/<?php echo $product['image']; ?>" 
                                     class="card-img-top" alt="<?php echo $product['product_name']; ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <img src="/E-comm/assets/images/no-image.png" 
                                     class="card-img-top" alt="No image" 
                                     style="width: 100%; height: 100%; object-fit: contain;">
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $product['product_name']; ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo substr($product['description'], 0, 60) . '...'; ?>
                            </p>
                            <div class="mb-3">
                                <span class="badge bg-secondary"><?php echo $product['category_name']; ?></span>
                            </div>
                            <p class="h5 text-primary mb-2"><?php echo formatPrice($product['price']); ?></p>
                            <div class="d-grid gap-2">
                                <a href="/E-comm/pages/product-detail.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <?php if (isUserLoggedIn()): ?>
                                    <button class="btn btn-sm btn-primary add-to-cart-btn" 
                                            data-product-id="<?php echo $product['product_id']; ?>">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="/E-comm/pages/login.php" class="btn btn-sm btn-primary">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5>Fast Shipping</h5>
                    <p class="text-muted">Quick and reliable delivery to your doorstep</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h5>Easy Returns</h5>
                    <p class="text-muted">30-day money-back guarentee</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5>24/7 Support</h5>
                    <p class="text-muted">Customer service always available</p>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="/E-comm/assets/js/script.js"></script>
</body>
</html>


