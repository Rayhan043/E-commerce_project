<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    
    $get_product_query = "SELECT image FROM products WHERE product_id = ?";
    $get_stmt = $conn->prepare($get_product_query);
    $get_stmt->bind_param("i", $product_id);
    $get_stmt->execute();
    $product_result = $get_stmt->get_result();
    
    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        
        $delete_query = "DELETE FROM products WHERE product_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $product_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        if (!empty($product['image'])) {
            $delete_upload_dir = dirname(__DIR__) . '/uploads/products/';
            deleteImage($product['image'], $delete_upload_dir);
        }
        
        showSuccess("Product deleted successfully");
    }
    
    $get_stmt->close();
    redirect('/E-comm/admin/products.php');
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE p.product_name LIKE '%" . $conn->real_escape_string($search) . "%'" : "";

$query = "SELECT p.*, c.category_name FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          {$where}
          ORDER BY p.created_at DESC";

$products_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/E-comm/admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/E-comm/admin/products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/categories.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-comm/admin/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="fas fa-boxes"></i> Manage Products</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="/E-comm/admin/add-product.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search products..." 
                               value="<?php echo $search; ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if ($products_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $products_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $product['product_id']; ?></td>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td><?php echo $product['category_name']; ?></td>
                                        <td><?php echo formatPrice($product['price']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $product['stock'] > 20 ? 'bg-success' : ($product['stock'] > 0 ? 'bg-warning' : 'bg-danger'); ?>">
                                                <?php echo $product['stock']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/E-comm/admin/edit-product.php?id=<?php echo $product['product_id']; ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $product['product_id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        No products found. <a href="/E-comm/admin/add-product.php">Add one now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


