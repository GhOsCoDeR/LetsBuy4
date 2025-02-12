<?php
include 'db_config.php';

// Ensure required fields are set
if (!isset($_POST['product_name'], $_POST['category_id'], $_POST['subcategory_id'], $_POST['price'], $_POST['description'], $_POST['product_url'])) {
    die("<script>alert('Error: Missing required fields.'); window.history.back();</script>");
}

$product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
$category_id = (int) $_POST['category_id'];
$subcategory_id = (int) $_POST['subcategory_id'];
$price = $_POST['price'];
$description = mysqli_real_escape_string($conn, $_POST['description']);
$product_url = mysqli_real_escape_string($conn, $_POST['product_url']);

// Handle main product image
$target_dir = "uploads/";
$main_image_name = basename($_FILES["product_image"]["name"]);
$main_image_path = $target_dir . $main_image_name;
move_uploaded_file($_FILES["product_image"]["tmp_name"], $main_image_path);

// Insert main product into the database
$sql = "INSERT INTO products (name, category_id, subcategory_id, price, description, image, product_url) 
        VALUES ('$product_name', '$category_id', '$subcategory_id', '$price', '$description', '$main_image_name', '$product_url')";

if (mysqli_query($conn, $sql)) {
    $product_id = mysqli_insert_id($conn); // Get the last inserted product ID

    // Insert variations if provided
    if (!empty($_POST['colors']) && !empty($_FILES['images']['name'][0])) {
        $stmt = $conn->prepare("INSERT INTO product_variations (product_id, variation, variation_image) VALUES (?, ?, ?)");

        foreach ($_POST['colors'] as $index => $color) {
            if (!empty($_FILES["images"]["name"][$index])) {
                $color = mysqli_real_escape_string($conn, $color);
                $variation_image_name = basename($_FILES["images"]["name"][$index]);
                $variation_image_path = $target_dir . $variation_image_name;
                move_uploaded_file($_FILES["images"]["tmp_name"][$index], $variation_image_path);

                $stmt->bind_param("iss", $product_id, $color, $variation_image_name);
                $stmt->execute();
            }
        }
        $stmt->close();
    }

    echo "<script>alert('Product and variations added successfully!'); window.location.href='add_product.php';</script>";
} else {
    echo "<script>alert('Database Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

$conn->close();
?>
