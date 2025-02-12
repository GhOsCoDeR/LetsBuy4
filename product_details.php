<?php
include 'db_config.php';

// Fetch product details
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $product = $result->fetch_assoc();
    $query->close();
}

// Fetch variations
$variations = [];
$var_query = $conn->prepare("SELECT variation, variation_image FROM product_variations WHERE product_id = ?");
$var_query->bind_param("i", $id);
$var_query->execute();
$var_result = $var_query->get_result();
while ($row = $var_result->fetch_assoc()) {
    $variations[] = $row;
}
$var_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | Product Details</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script> 
    <style>
        /* Product Page Layout */
        .product-container {
            display: flex;
            max-width: 1100px;
            margin: auto;
            padding: 20px;
            gap: 30px;
        }

        /* Product Image */
        .product-image {
            width: 50%;
            text-align: center;
        }

        .product-image img {
            width: 100%;
            max-height: 500px;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Product Details */
        .product-info {
            width: 50%;
        }

        .product-info h1 {
            font-size: 24px;
            font-weight: bold;
        }

        .product-info .ratings {
            color: #f7c000;
            font-size: 16px;
        }

        .price {
            font-size: 22px;
            font-weight: bold;
            color: #d00;
        }

        .old-price {
            text-decoration: line-through;
            color: grey;
            margin-left: 10px;
        }

        .discount {
            color: green;
            font-weight: bold;
        }

        .delivery-info {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* Variations */
        .variation-select {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Buttons */
        .btn-add {
            display: block;
            width: 100%;
            padding: 12px;
            background: #ffcc00;
            border: none;
            cursor: pointer;
            font-size: 18px;
            margin-top: 10px;
        }

        .btn-add:hover {
            background: #ff9900;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background: #555;
        }
    </style>
</head>
<body>

    <div class="product-container">
        <!-- Product Image Section -->
        <div class="product-image">
            <img id="product-image" src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 onerror="this.src='uploads/default.jpg';">
        </div>

        <!-- Product Info Section -->
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="ratings">
                ⭐⭐⭐⭐⭐ <span>(300 sold)</span>
            </p>

            <p class="price">
                GHC <?php echo number_format($product['price'], 2); ?>
                <span class="old-price">GHC <?php echo number_format($product['price'] + 31, 2); ?></span>
                <span class="discount">You save: GHC 31 (8%)</span>
            </p>

            <div class="delivery-info">
                <p>🚚 Delivery: <strong>GHC 50</strong></p>
                <p>📅 Estimated delivery: <strong>14 Feb - 17 Feb</strong> if ordered today</p>
            </div>

            <!-- Variations Dropdown -->
            <?php if (!empty($variations)): ?>
                <label for="variation">* Select Color:</label>
                <select id="variation" class="variation-select" onchange="updateProductImage()">
                    <option value="" data-image="uploads/<?php echo htmlspecialchars($product['image']); ?>">Please select</option>
                    <?php foreach ($variations as $var): ?>
                        <option value="<?php echo htmlspecialchars($var['variation']); ?>" 
                                data-image="uploads/<?php echo htmlspecialchars($var['variation_image']); ?>">
                            <?php echo ucfirst(htmlspecialchars($var['variation'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <!-- Add to Cart Button -->
            <button class="btn-add" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Basket</button>

            <p><strong>Condition:</strong> Brand New</p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($product['category_id']); ?></p>
            <p><strong>Item Number:</strong> 000000<?php echo $product['id']; ?></p>

            <a href="index.php" class="back-btn">← Back to Shop</a>
        </div>
    </div>

    <script>
        function addToCart(productId) {
            const variation = document.getElementById("variation") ? document.getElementById("variation").value : "";

            fetch("add_to_cart.php", {
                method: "POST",
                body: new URLSearchParams({ product_id: productId, variation: variation }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Added to cart!");
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    </script>

    <script>
        function updateProductImage() {
            var select = document.getElementById("variation");
            var selectedOption = select.options[select.selectedIndex];
            var newImage = selectedOption.getAttribute("data-image");

            if (newImage) {
                document.getElementById("product-image").src = newImage;
            }
        }
    </script>

</body>
</html>
