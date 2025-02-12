<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $sql = "UPDATE products SET name='$name', price='$price', category='$category' WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: manage.php?msg=Product updated successfully");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <main class="main-content">
        <h1>Edit Product</h1>
        <form method="post">
            <label>Product Name:</label>
            <input type="text" name="name" value="<?= $product['name'] ?>" required>

            <label>Price (GHC):</label>
            <input type="text" name="price" value="<?= $product['price'] ?>" required>

            <label>Category:</label>
            <input type="text" name="category" value="<?= $product['category'] ?>" required>

            <button type="submit">Update Product</button>
        </form>
    </main>
</body>
</html>
