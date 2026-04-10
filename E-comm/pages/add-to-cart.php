<?php


header('Content-Type: application/json');

include_once '../config/db.php';
include_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

$query = "SELECT product_id, stock FROM products WHERE product_id = ? AND stock > 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found or out of stock']);
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

$check_query = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $cart = $check_result->fetch_assoc();
    $new_quantity = $cart['quantity'] + $quantity;

    if ($new_quantity > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock']);
        exit;
    }

    $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
    $update_stmt->execute();
    $update_stmt->close();
} else {
    if ($quantity > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock']);
        exit;
    }

    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$check_stmt->close();

$cart_count = getCartItemCount($conn, $user_id);

echo json_encode([
    'success' => true,
    'message' => 'Product added to cart',
    'cart_count' => $cart_count
]);
?>



