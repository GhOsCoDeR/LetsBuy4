<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get form data
$billing_firstname = $_POST['billing_firstname'];
$billing_lastname = $_POST['billing_lastname'];
$billing_contact = $_POST['billing_contact'];
$billing_street = $_POST['billing_street'];
$billing_city = $_POST['billing_city'];
$billing_country = $_POST['billing_country'];

// Shipping details (optional)
$shipping_firstname = $_POST['shipping_firstname'] ?? null;
$shipping_lastname = $_POST['shipping_lastname'] ?? null;
$shipping_street = $_POST['shipping_street'] ?? null;
$shipping_city = $_POST['shipping_city'] ?? null;
$shipping_country = $_POST['shipping_country'] ?? null;

// Payment details
$payment_method = $_POST['payment_method'];
$card_number = $_POST['card_number'] ?? null;
$card_expiry = $_POST['card_expiry'] ?? null;
$card_cvv = $_POST['card_cvv'] ?? null;
$mobile_money_network = $_POST['mobile_money_network'] ?? null;
$mobile_money_number = $_POST['mobile_money_number'] ?? null;

// Calculate total price
$total_price_query = $conn->query("SELECT SUM(products.price * cart.quantity) AS total FROM cart 
                                   JOIN products ON cart.product_id = products.id 
                                   WHERE cart.user_id = '$user_id'");
$total_price = $total_price_query->fetch_assoc()['total'] ?? 0;

if ($total_price == 0) {
    echo "<script>alert('Your cart is empty.'); window.location.href='cart.php';</script>";
    exit();
}

// Fix: Ensure all fields are included and types match
$stmt = $conn->prepare("INSERT INTO orders 
    (user_id, billing_firstname, billing_lastname, billing_contact, billing_street, billing_city, billing_country, 
     shipping_firstname, shipping_lastname, shipping_street, shipping_city, shipping_country, 
     payment_method, card_number, card_expiry, card_cvv, mobile_money_network, mobile_money_number, total_price) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("isssssssssssssssssd", 
    $user_id, 
    $billing_firstname, 
    $billing_lastname, 
    $billing_contact, 
    $billing_street, 
    $billing_city, 
    $billing_country, 
    $shipping_firstname, 
    $shipping_lastname, 
    $shipping_street, 
    $shipping_city, 
    $shipping_country, 
    $payment_method, 
    $card_number, 
    $card_expiry, 
    $card_cvv, 
    $mobile_money_network, 
    $mobile_money_number, 
    $total_price
);

if ($stmt->execute()) {
    // Clear cart after successful order
    $conn->query("DELETE FROM cart WHERE user_id = '$user_id'");

    echo "<script>alert('Order placed successfully!'); window.location.href='orders.php';</script>";
} else {
    echo "<script>alert('Error processing order. Please try again.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
