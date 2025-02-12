<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to view your favorites.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch categories with favorited products
$categories_result = $conn->query("
    SELECT DISTINCT c.id AS category_id, c.name AS category_name 
    FROM favorites f
    JOIN products p ON f.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    WHERE f.user_id = '$user_id'
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites</title>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <style>
        
html {
    scroll-behavior: smooth;
}



/* NAVIGATION */
nav {
    background: #444;
}

nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    padding: 10px 0;
}

nav ul li {
    padding: 10px 20px;
    position: relative;
}

nav ul li a {
    color: white;
    text-decoration: none;
}
        /* Product Card Styling */
        .product-card {
            text-align: center;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            position: relative;
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
        }

        .product-card h3 {
            font-size: 16px;
            margin-top: 10px;
        }

        .product-card p {
            color: #e91e63;
            font-weight: bold;
        }

        .product-card .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #ff4081;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #e91e63;
        }

        /* Remove Favorite Button */
        .remove-favorite-btn {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
            margin-top: 10px;
        }

        .remove-favorite-btn:hover {
            background: #cc0000;
        }

        /* Swiper Styles */
        .swiper-button-next, .swiper-button-prev {
            color: #ff4081;
        }

        .swiper-container {
            padding: 20px;
        }

        .secondary-nav {
            background: #333;
            padding: 10px;
            text-align: center;
            margin-top: 1px;
        }
        
.logo {
    font-size: 24px;
    font-weight: bold;
    color: #ff5733;
}

/* User icons styling */
.user-icons a {
    color: white;
    font-size: 20px;
    margin-left: 15px;
    text-decoration: none;
    transition: color 0.3s ease;
}

.user-icons a:hover {
    color: #f8c24f; /* Highlight color on hover */
}

.user-icons span {
    margin-left: 5px;
    font-weight: bold;
}
/* Search bar styling */
.search-bar {
    display: flex;
    align-items: center;
    background: white;
    padding: 5px;
    border-radius: 20px;
    width: 300px;
}

.search-bar input {
    border: none;
    outline: none;
    padding: 8px;
    width: 100%;
    border-radius: 20px;
}

.search-bar button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px 10px;
    color: #555;
    font-size: 18px;
}

    </style>
</head>
<body>

<!-- Secondary Navigation -->
<nav class="secondary-nav" style="position: relative;">
    <button class="back-btn" onclick="goBack()">
        <i class="fas fa-arrow-left"></i> Back
    </button>
    <h1>Your Favorite Products</h1>
</nav>

<script>
    function goBack() {
        window.history.back();
    }
</script>


<style>
    .secondary-nav h1{
        color:white;

    }
   .back-btn {
    background: #ff4081;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    position: absolute;
    left: 20px; /* Adjust as needed */
    top: 50%;
    transform: translateY(-50%);
}


    .back-btn i {
        margin-right: 8px;
    }

    .back-btn:hover {
        background: #e91e63;
    }
</style>

<div class="container">
  

    <?php while ($category_row = $categories_result->fetch_assoc()) : ?>
        <?php 
            $category_id = $category_row['category_id'];
            $category_name = $category_row['category_name']; 
        ?>

        <section class='products' id='category-<?php echo $category_id; ?>'>
            <h2><?php echo htmlspecialchars($category_name); ?></h2>

            <div class='swiper mySwiper'>
                <div class='swiper-wrapper'>

                    <?php
                    // Fetch favorited products in this category
                    $products_result = $conn->query("
                        SELECT p.* FROM favorites f
                        JOIN products p ON f.product_id = p.id
                        WHERE f.user_id = '$user_id' AND p.category_id = '$category_id'
                    ");

                    while ($row = $products_result->fetch_assoc()) :
                        $image_path = !empty($row['image']) ? "uploads/{$row['image']}" : "uploads/default.jpg";
                        $product_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class='swiper-slide product-card'>
                            <img src='<?php echo $image_path; ?>' 
                                alt='<?php echo htmlspecialchars($row['name']); ?>' 
                                onclick='showProductPopup(<?php echo $product_json; ?>)' 
                                onerror="this.src='uploads/default.jpg';">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p>GHC <?php echo $row['price']; ?></p>
                            <a href='#' class='btn' onclick='addToCart(<?php echo $row['id']; ?>)'>Add to Cart</a>
                            <button class="remove-favorite-btn" data-id="<?php echo $row['id']; ?>">Remove</button>
                        </div>
                    <?php endwhile; ?>

                </div>

                <!-- Swiper Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>

    <?php endwhile; ?>
</div>

<!-- Swiper JS -->
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

    // Handle Remove Favorite
    document.querySelectorAll('.remove-favorite-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            let productId = this.getAttribute('data-id');
            
            fetch('remove_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        });
    });
</script>

</body>
</html>
