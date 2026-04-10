<?php



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function getCurrentUserId() {
    return isUserLoggedIn() ? $_SESSION['user_id'] : null;
}

function getCurrentAdminId() {
    return isAdminLoggedIn() ? $_SESSION['admin_id'] : null;
}

function showSuccess($message) {
    $_SESSION['success_message'] = $message;
}

function showError($message) {
    $_SESSION['error_message'] = $message;
}

function getSuccessMessage() {
    if (isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        return $message;
    }
    return false;
}

function getErrorMessage() {
    if (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        return $message;
    }
    return false;
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function getCartTotal($conn, $user_id) {
    $query = "SELECT SUM(c.quantity * p.price) as total 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'] ?? 0;
}

function getCartItemCount($conn, $user_id) {
    $query = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] ?? 0;
}

function generateOrderNumber() {
    return "ORD-" . date('Ymd') . "-" . strtoupper(uniqid());
}

function isValidImageUpload($file) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed)) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        return false;
    }
    
    return true;
}

function uploadImage($file, $upload_dir = 'uploads/products/') {
    if (!isValidImageUpload($file)) {
        return false;
    }
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

function deleteImage($filename, $upload_dir = 'uploads/products/') {
    $filepath = $upload_dir . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        return true;
    }
    return false;
}

function getStockStatus($stock) {
    if ($stock > 20) {
        return '<span class="badge bg-success">In Stock</span>';
    } elseif ($stock > 0) {
        return '<span class="badge bg-warning">Low Stock</span>';
    } else {
        return '<span class="badge bg-danger">Out of Stock</span>';
    }
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y H:i A', strtotime($datetime));
}

?>


