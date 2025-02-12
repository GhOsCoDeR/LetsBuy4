<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Check if the product is already in the cart
$check = $conn->query("SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
if ($check->num_rows > 0) {
    // Update quantity if already in cart
    $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
} else {
    // Add new product to cart
    $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', 1)");
}

// Get updated cart count
$count_result = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id='$user_id'");
$count = $count_result->fetch_assoc()['total'];

echo json_encode(["status" => "success", "cart_count" => $count]);
?>
