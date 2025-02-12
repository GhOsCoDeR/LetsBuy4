<?php
session_start();
include 'db_config.php';

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $conn->query("DELETE FROM cart WHERE id = '$cart_id'");
}

header("Location: cart.php");
exit();
?>
