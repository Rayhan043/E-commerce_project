<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

$error = '';
$success = '';

if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    if (empty($name)) {
        $error = "Category name is required";
    } else {
        $insert = "INSERT INTO categories (category_name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            $success = "Category added successfully";
        } else {
            $error = "Failed to add category";
        }
        $stmt->close();
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('/E-comm/admin/categories.php');
}

$result = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/E-comm/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link active" href="/E-comm/admin/categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h1 class="mb-4"><i class="fas fa-th"></i> Manage Categories</h1>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Category Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Categories List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($cat = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $cat['category_id']; ?></td>
                                            <td><?php echo $cat['category_name']; ?></td>
                                            <td><?php echo substr($cat['description'], 0, 40); ?></td>
                                            <td>
                                                <a href="?delete=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


