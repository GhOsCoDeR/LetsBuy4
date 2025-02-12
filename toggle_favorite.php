<?php
session_start();
include 'db_config.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;

if ($product_id == 0) {
    echo json_encode(["status" => "error", "message" => "Invalid product ID."]);
    exit();
}

// Check if the product is already in favorites
$check = $conn->query("SELECT * FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");

if ($check->num_rows > 0) {
    // Remove from favorites
    $conn->query("DELETE FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");
    echo json_encode(["status" => "removed"]);
} else {
    // Add to favorites
    $conn->query("INSERT INTO favorites (user_id, product_id) VALUES ('$user_id', '$product_id')");
    echo json_encode(["status" => "added"]);
}
?>
