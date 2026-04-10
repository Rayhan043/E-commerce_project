<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('/E-comm/admin/');
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('/E-comm/admin/users.php');
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE first_name LIKE '%" . $conn->real_escape_string($search) . "%' OR email LIKE '%" . $conn->real_escape_string($search) . "%'" : "";

$result = $conn->query("SELECT * FROM users {$where} ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/E-comm/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="/E-comm/admin/users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="/E-comm/admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h1 class="mb-4"><i class="fas fa-users"></i> Manage Users</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search by name or email..." value="<?php echo $search; ?>">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Users List (Total: <?php echo $result->num_rows; ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo formatDate($user['created_at']); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">
                                            <i class="fas fa-trash"></i> Delete
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


