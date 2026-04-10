<?php


$page_title = "Shop";
include_once '../config/db.php';
include_once '../includes/functions.php';

$items_per_page = 12;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$category_filter = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : 0;
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';

$where = "WHERE 1=1";
$params = [];
$param_types = "";

if ($category_filter > 0) {
    $where .= " AND p.category_id = ?";
    $params[] = $category_filter;
    $param_types .= "i";
}

if (!empty($search_query)) {
    $where .= " AND (p.product_name LIKE ? OR p.description LIKE ?)";
    $search_term = "%{$search_query}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= "ss";
}

$order_by = "p.created_at DESC";
if ($sort_by === 'price_low') {
    $order_by = "p.price ASC";
} elseif ($sort_by === 'price_high') {
    $order_by = "p.price DESC";
} elseif ($sort_by === 'name') {
    $order_by = "p.product_name ASC";
}

$count_query = "SELECT COUNT(*) as total FROM products p {$where} AND p.stock > 0";
$count_stmt = $conn->prepare($count_query);

if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_items = $count_row['total'];
$total_pages = ceil($total_items / $items_per_page);

$query = "SELECT p.*, c.category_name FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          {$where} AND p.stock > 0
          ORDER BY {$order_by}
          LIMIT ? OFFSET ?";

$limit = $items_per_page;
$params[] = $limit;
$params[] = $offset;
$param_types .= "ii";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$products_result = $stmt->get_result();

$categories_query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-shopping-bag"></i> Shop</h1>

        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="/E-comm/pages/shop.php" id="filterForm">
                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Search</label>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Product name..." value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Category</label>
                                <select class="form-select" name="category" id="categorySelect">
                                    <option value="0">All Categories</option>
                                    <?php 
                                    $categories_result->data_seek(0);
                                    while ($cat = $categories_result->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $cat['category_id']; ?>" 
                                                <?php echo $category_filter == $cat['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Sort by</label>
                                <select class="form-select" name="sort" id="sortSelect">
                                    <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest</option>
                                    <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name (A-Z)</option>
                                    <option value="price_low" <?php echo $sort_by === 'price_low' ? 'selected' : ''; ?>>Price (Low to High)</option>
                                    <option value="price_high" <?php echo $sort_by === 'price_high' ? 'selected' : ''; ?>>Price (High to Low)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="applyFiltersBtn">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>

                            <?php if ($search_query || $category_filter > 0): ?>
                                <a href="/E-comm/pages/shop.php" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="mb-3 text-muted">
                    <p>Showing <strong><?php echo ($offset + 1); ?></strong> to <strong><?php echo min($offset + $items_per_page, $total_items); ?></strong> of <strong><?php echo $total_items; ?></strong> products</p>
                </div>

                <?php if ($products_result->num_rows > 0): ?>
                    <div class="row g-4">
                        <?php while ($product = $products_result->fetch_assoc()): ?>
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
                                                <button class="btn btn-sm btn-primary add-to-cart-btn" data-product-id="<?php echo $product['product_id']; ?>">
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

                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-5" aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1<?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>&sort=<?php echo $sort_by; ?>">First</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>&sort=<?php echo $sort_by; ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $current_page - 2);
                                $end = min($total_pages, $current_page + 2);
                                
                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>&sort=<?php echo $sort_by; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>&sort=<?php echo $sort_by; ?>">Next</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>&sort=<?php echo $sort_by; ?>">Last</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> No products found. Try adjusting your filters.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="/E-comm/assets/js/script.js"></script>
</body>
</html>


