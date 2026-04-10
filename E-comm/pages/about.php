<?php


$page_title = "About Us";
include_once '../config/db.php';
include_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4"><i class="fas fa-info-circle"></i> About Us</h1>

        <div class="row mb-5">
            <div class="col-md-6">
                <h2>Welcome to ECommerce Store</h2>
                <p>We are a leading online retailer committed to providing the best shopping experience for our customers worldwide.</p>
                <p>With over a decade of experience in e-commerce, we pride ourselves on our quality products, competitive prices, and exceptional customer service.</p>
                <p>Our mission is to make online shopping easy, secure, and enjoyable for everyone.</p>
            </div>
            <div class="col-md-6">
                <div class="bg-light p-4 rounded">
                    <i class="fas fa-store fa-5x text-primary mb-3"></i>
                    <h3>Our Vision</h3>
                    <p>To be the most trusted online shopping destination, offering a wide variety of high-quality products at unbeatable prices.</p>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-3 text-center">
                <i class="fas fa-truck fa-3x text-success mb-3"></i>
                <h4>Fast Shipping</h4>
                <p>Quick and reliable delivery</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h4>Secure</h4>
                <p>Your data is safe with us</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-headset fa-3x text-info mb-3"></i>
                <h4>Support</h4>
                <p>24/7 Customer support</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-dollar-sign fa-3x text-warning mb-3"></i>
                <h4>Best Prices</h4>
                <p>Competitive pricing always</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h3>Why Choose Us?</h3>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i> Wide selection of products</li>
                    <li><i class="fas fa-check text-success me-2"></i> Competitive prices</li>
                    <li><i class="fas fa-check text-success me-2"></i> Fast and free shipping over $50</li>
                    <li><i class="fas fa-check text-success me-2"></i> Easy returns within 30 days</li>
                    <li><i class="fas fa-check text-success me-2"></i> Secure payment options</li>
                    <li><i class="fas fa-check text-success me-2"></i> Expert customer support</li>
                </ul>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


