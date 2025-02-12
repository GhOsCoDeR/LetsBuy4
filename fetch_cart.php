<?php
session_start();
include 'db_config.php';

$user_id = $_SESSION['user_id'] ?? 0;

$query = "SELECT p.name, p.price, c.quantity, c.product_id AS id FROM cart c 
          JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

echo json_encode(["items" => $cartItems]);
?>
