<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

$page_title = isset($_GET['id']) ? "Edit Product" : "Add Product";
$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$error = '';

$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");

if ($product_id > 0) {
    $query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        redirect('/E-comm/admin/products.php');
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $product_name = sanitize($_POST['product_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $sku = sanitize($_POST['sku'] ?? '');

    $errors = [];

    if (empty($product_name)) $errors[] = "Product name is required";
    if ($price <= 0) $errors[] = "Price must be greater than 0";
    if ($category_id <= 0) $errors[] = "Category must be selected";

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if (isValidImageUpload($_FILES['image'])) {
            $upload_dir = dirname(__DIR__) . '/uploads/products/';
            $image = uploadImage($_FILES['image'], $upload_dir);
            if (!$image) {
                $errors[] = "Failed to upload image";
            }
        } else {
            $errors[] = "Invalid image file or file too large";
        }
    }

    if (empty($errors)) {
        if ($product_id > 0) {
            if ($image) {
                if ($product['image']) {
                    $delete_upload_dir = dirname(__DIR__) . '/uploads/products/';
                    deleteImage($product['image'], $delete_upload_dir);
                }
                $update_query = "UPDATE products SET category_id = ?, product_name = ?, description = ?, 
                               price = ?, stock = ?, sku = ?, image = ? WHERE product_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("issdissi", $category_id, $product_name, $description, $price, 
                                        $stock, $sku, $image, $product_id);
            } else {
                $update_query = "UPDATE products SET category_id = ?, product_name = ?, description = ?, 
                               price = ?, stock = ?, sku = ? WHERE product_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("issdisi", $category_id, $product_name, $description, $price, 
                                        $stock, $sku, $product_id);
            }

            if ($update_stmt->execute()) {
                showSuccess("Product updated successfully");
                redirect('/E-comm/admin/products.php');
            } else {
                $error = "Failed to update product";
            }
            $update_stmt->close();
        } else {
            if (!$image) {
                $errors[] = "Product image is required";
            } else {
                $insert_query = "INSERT INTO products (category_id, product_name, description, price, stock, sku, image) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("issdiss", $category_id, $product_name, $description, $price, 
                                        $stock, $sku, $image);

                if ($insert_stmt->execute()) {
                    showSuccess("Product added successfully");
                    redirect('/E-comm/admin/products.php');
                } else {
                    $error = "Failed to add product";
                }
                $insert_stmt->close();
            }
        }
    }

    if ($errors) {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/E-comm/admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Admin Panel
            </a>
            <div class="ms-auto">
                <a href="/E-comm/admin/logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-boxes"></i> <?php echo $page_title; ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select a category</option>
                                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                            <option value="<?php echo $cat['category_id']; ?>" 
                                                    <?php echo ($product && $product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo $cat['category_name']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="product_name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" 
                                           value="<?php echo $product ? $product['product_name'] : ''; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo $product ? $product['description'] : ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Price (USD) *</label>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0" value="<?php echo $product ? $product['price'] : ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="stock" class="form-label">Stock *</label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           min="0" value="<?php echo $product ? $product['stock'] : ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="sku" name="sku" 
                                           value="<?php echo $product ? $product['sku'] : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image <?php echo !$product ? '*' : ''; ?></label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/*" <?php echo !$product ? 'required' : ''; ?>>
                                <?php if ($product && $product['image']): ?>
                                    <small class="form-text text-muted">Current image: <?php echo $product['image']; ?></small>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> <?php echo $product_id > 0 ? 'Update' : 'Create'; ?> Product
                            </button>

                            <a href="/E-comm/admin/products.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


