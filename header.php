<?php

include 'db_config.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch cart count
$cart_count = 0;
if ($is_logged_in) {
    $cart_query = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id='$user_id'");
    $cart_count = $cart_query->fetch_assoc()['total'] ?? 0;
}

// Fetch favorites count
$fav_count = 0;
if ($is_logged_in) {
    $fav_query = $conn->query("SELECT COUNT(*) AS count FROM favorites WHERE user_id='$user_id'");
    $fav_count = $fav_query->fetch_assoc()['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My E-Commerce Site</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #222;
    padding: 15px 5%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: none;
}

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .cart-icon, .user-icon {
            font-size: 18px;
            cursor: pointer;
        }

        .cart-icon span, .favorite-count {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 12px;
            position: relative;
            top: -10px;
            left: -5px;
        }

        /* Navigation */
        
/* Ensure secondary navigation stays below */
.secondary-nav {
    background: #333;
    padding: 10px;
    text-align: center;
    margin-top:1px;
}
.search-list {
    list-style: none;
    background: white;
    border: 1px solid #ddd;
    padding: 10px;
    position: absolute;
    width: 250px;
    z-index: 1000;
}

.search-list li {
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.search-list li a {
    text-decoration: none;
    color: black;
    display: block;
}

.search-list li:hover {
    background: #f4f4f4;
}

.favorite-btn {
    font-size: 20px;
    color: #ff4d4d; /* Red for liked */
    transition: color 0.3s;
    cursor: pointer;
}

.favorite-btn .far {
    color: #ccc; /* Gray when not liked */
}

.favorite-btn .fas {
    color: #ff4d4d; /* Red when liked */
}

    </style>
</head>
<body data-logged-in="<?= $is_logged_in ? 'true' : 'false'; ?>">

<header>
<div class="logo">MyShop</div>

<div class="search-bar">
<input type="text" id="searchInput" placeholder="Search for products...">
<button id="searchBtn"><i class="fas fa-search"></i></button>
</div>
<div id="searchResults"></div> <!-- This div will display search results -->



<div class="user-icons">
<?php
include 'db_config.php';

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id='$user_id'");
$cart_count = $result->fetch_assoc()['total'] ?? 0;
}
?>

<div class="cart-icon">
<a href="cart.php">
    🛒 <span id="cartCount"><?php echo $cart_count; ?></span>
</a>
</div>
</header>

<!-- Secondary Navigation -->
<nav class="secondary-nav">
    <ul>
        <li><a href="order_status.php"><i class="fas fa-truck"></i> Order Status</a></li>
        <li><a href="account.php"><i class="fas fa-user-circle"></i> Accounts</a></li>
        <li>
            <a href="favorites.php">
                <i class="fa fa-heart"></i> Saved Items <span class="favorite-count"><?= $fav_count; ?></span>
            </a>
        </li>
    </ul>
</nav>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let isLoggedIn = document.body.dataset.loggedIn === "true";

        // Handle favorite button click
        document.body.addEventListener("click", function (event) {
            if (event.target.classList.contains("favorite-btn")) {
                event.preventDefault();
                if (!isLoggedIn) {
                    alert("Please log in to save favorites.");
                    return;
                }

                let productId = event.target.getAttribute("data-id");
                let icon = event.target.querySelector("i");

                fetch("toggle_favorite.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `product_id=${productId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "added") {
                        icon.classList.remove("far");
                        icon.classList.add("fas");
                    } else if (data === "removed") {
                        icon.classList.remove("fas");
                        icon.classList.add("far");
                    }
                });
            }
        });

        // Handle add-to-cart button click
        document.body.addEventListener("click", function (event) {
            if (event.target.classList.contains("add-to-cart")) {
                event.preventDefault();

                let productId = event.target.getAttribute("data-id");
                if (!productId) {
                    alert("Error: Product ID is missing.");
                    return;
                }

                fetch("add_to_cart.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        updateCartCount(data.cart_count);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });

        function updateCartCount(count) {
            let cartIcon = document.getElementById("cart-count");
            if (cartIcon) {
                cartIcon.textContent = count;
            }
        }
    });
</script>

</body>
</html>
