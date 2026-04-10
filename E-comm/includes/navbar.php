<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/E-comm/">
            <i class="fas fa-shopping-bag"></i> ECommerce Store
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php
                $isUser = isset($_SESSION['user_id']);
                $isAdmin = isset($_SESSION['admin_id']);
                ?>

                <?php if ($isUser): ?>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/shop.php">Shop</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/orders.php">Orders</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/logout.php">Logout</a>
                    </li>
                <?php elseif ($isAdmin): ?>
                    
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/admin/logout.php">Logout</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/shop.php">Shop</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/orders.php">Orders</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Account'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="/E-comm/pages/profile.php">My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/E-comm/pages/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/shop.php">Shop</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="/E-comm/pages/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/E-comm/pages/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


