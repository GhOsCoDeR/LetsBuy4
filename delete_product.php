<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: manage.php?msg=Product deleted successfully");
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}
?>
