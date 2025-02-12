<?php
include 'db_config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;

if ($user_id && $product_id) {
    // Check if product is already in favorites
    $result = $conn->query("SELECT * FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");

    if ($result->num_rows > 0) {
        // Remove from favorites
        $conn->query("DELETE FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");
        echo json_encode(["status" => "removed"]);
    } else {
        // Add to favorites
        $conn->query("INSERT INTO favorites (user_id, product_id) VALUES ('$user_id', '$product_id')");
        echo json_encode(["status" => "added"]);
    }
}
?>
