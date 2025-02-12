<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$cart_id || !in_array($action, ['increase', 'decrease'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

// Fetch the current quantity
$query = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $cart_id, $user_id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["success" => false, "message" => "Cart item not found."]);
    exit();
}

$current_quantity = $row['quantity'];

// Update quantity based on action
if ($action == "increase") {
    $new_quantity = $current_quantity + 1;
} elseif ($action == "decrease") {
    $new_quantity = max(1, $current_quantity - 1); // Prevent quantity from going below 1
}

// Update the cart with the new quantity
$update_query = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
$update_query->bind_param("iii", $new_quantity, $cart_id, $user_id);
$update_query->execute();

echo json_encode(["success" => true, "new_quantity" => $new_quantity]);
?>
