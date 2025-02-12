<?php
session_start();
include 'db_config.php';
include 'header.php';

$user_id = $_SESSION['user_id'] ?? 0;

// Ensure category is set
if (isset($_GET['category_id'])) {
    $category_id = (int) $_GET['category_id'];

    // Fetch category name
    $category_query = $conn->query("SELECT name FROM categories WHERE id = $category_id");
    $category = $category_query->fetch_assoc();

    // Fetch subcategories
    $subcategories = $conn->query("SELECT * FROM subcategories WHERE category_id = $category_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More Products</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
        .products {
            padding: 20px;
            text-align: center;
        }

        .products h2 {
            margin-bottom: 20px;
            color: #222;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 10px;
        }

        .product-card {
            width: 250px;
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background: #fff;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }

        .product-card h3 {
            margin: 10px 0;
            font-size: 16px;
        }

        .product-card p {
            font-size: 18px;
            font-weight: bold;
            color: #f04e30;
        }

        .btn {
            display: inline-block;
            background: #f04e30;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn:hover {
            background: #d13b25;
        }
        
        /* Favorite Button */
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
        }

        /* Swiper Navigation */
        .swiper-button-next, .swiper-button-prev {
            color: #f04e30;
        }
    </style>
</head>
<body>

<section class="products">
    <h2>Products in <?php echo htmlspecialchars($category['name'] ?? ''); ?></h2>

    <?php while ($sub = $subcategories->fetch_assoc()): ?>
        <h3><?php echo htmlspecialchars($sub['subcategory_name']); ?></h3>

        <div class="swiper mySwiper">
            <div class="swiper-wrapper">

                <?php
                $sub_id = $sub['id'];
                $products = $conn->query("SELECT * FROM products WHERE subcategory_id = $sub_id");

                if ($products->num_rows > 0) {
                    while ($row = $products->fetch_assoc()) {
                        $image_path = !empty($row['image']) ? "uploads/{$row['image']}" : "uploads/default.jpg";
                        $product_id = $row['id'];

                        // Check if the product is in favorites
                        $fav_check = $conn->query("SELECT * FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");
                        $is_favorited = ($fav_check->num_rows > 0);
                ?>
                        <div class="swiper-slide product-card">
                            <img src='<?php echo $image_path; ?>' alt='<?php echo htmlspecialchars($row['name']); ?>' 
                                onclick="window.location.href='product_details.php?id=<?php echo $product_id; ?>'"
                                onerror="this.src='uploads/default.jpg';">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p>GHC <?php echo $row['price']; ?></p>

                            <!-- Favorite Button -->
                            <button class="favorite-btn" data-id="<?= $product_id; ?>">
                                <i class="<?= $is_favorited ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>

                            <a href="#" class="btn add-to-cart" data-id="<?php echo $product_id; ?>">Add to Cart</a>
                        </div>
                <?php 
                    }
                } else {
                    echo "<p>No products found in this subcategory.</p>";
                }
                ?>

            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    <?php endwhile; ?>
</section>

<!-- Swiper.js -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 4,
        spaceBetween: 20,
        loop: false,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            320: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        }
    });

    // Function to update cart count
    function updateCartCount() {
        fetch("get_cart_count.php")
            .then(response => response.text())
            .then(count => {
                document.getElementById("cart-count").textContent = count;
            })
            .catch(error => console.error("Error:", error));
    }

    // Function to update favorite count
    function updateFavoriteCount() {
        fetch("get_favorite_count.php")
            .then(response => response.text())
            .then(count => {
                document.getElementById("favorite-count").textContent = count;
            })
            .catch(error => console.error("Error:", error));
    }

    // Add to Cart
document.body.addEventListener("click", function (event) {
    if (event.target.classList.contains("add-to-cart")) {
        event.preventDefault();
        
        const productId = event.target.getAttribute("data-id");

        // Disable button temporarily to prevent multiple clicks
        event.target.disabled = true;

        fetch("add_to_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                updateCartCount();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error:", error))
        .finally(() => {
            event.target.disabled = false; // Re-enable button after request
        });
    }
});

// Function to update cart count
function updateCartCount() {
    fetch("get_cart_count.php")
        .then(response => response.text())
        .then(count => {
            document.getElementById("cart-count").textContent = count;
        })
        .catch(error => console.error("Error:", error));
}

</script>

</body>
</html>
