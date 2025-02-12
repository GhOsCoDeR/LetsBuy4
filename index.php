<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My E-Commerce Site</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- SwiperJS CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
        /* Product Card */

.btn {
    display: inline-block;
    padding: 12px 20px;
    background-color: #007bff; /* Blue color */
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    border: none;
    border-radius: 5px;
    margin-top:10px;
    text-decoration: none;
    transition: all 0.3s ease-in-out;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.btn:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: translateY(-3px);
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
}

.btn:active {
    transform: translateY(1px);
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}


.product-card .btn:hover {
    background: #e91e63;
}

/* Swiper Navigation */
.swiper-button-next, .swiper-button-prev {
    color: #ff4081;
}
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



/* MOBILE STYLES */
@media screen and (max-width: 768px) {

    /* Hide navigation menu and add hamburger button */
    nav ul {
        display: none;
        flex-direction: column;
        background: #444;
        width: 100%;
        text-align: center;
    }

    nav ul.show {
        display: flex;
    }

    nav ul li {
        width: 100%;
    }

    /* Hamburger Menu */
    .menu-btn {
        display: block;
        font-size: 24px;
        cursor: pointer;
        color: white;
        background: none;
        border: none;
        padding: 10px;
    }

    /* Adjust hero section */
    .hero h1 {
        font-size: 28px;
    }

    .hero p {
        font-size: 16px;
    }

    /* Footer adjustments */
    .footer-content {
        flex-direction: column;
        align-items: center;
    }

    .footer-section {
        margin-bottom: 20px;
    }
}

@media screen and (max-width: 480px) {
    /* Adjust search bar width */
    .search-bar input {
        width: 150px;
    }

    /* Adjust product display */
    .product-card {
        width: 90%;
        margin: auto;
    }

    /* Adjust hero section */
    .hero h1 {
        font-size: 24px;
    }

    .hero p {
        font-size: 14px;
    }

    .swiper .swiper-slide {
        width: 100%;
    }
}




    </style>
</head>
<body>
<body data-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">


    <!-- HEADER (LOGO, SEARCH BAR, USER ICONS) -->
   
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



        <!-- <a href="cart.php"><i class="fas fa-shopping-cart"></i></a> -->

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-dropdown-container">
                <a href="#" id="userIcon">
                    <i class="fas fa-user"></i> 
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </a>

                <div class="user-dropdown" id="userDropdown">
                    <a href="settings.php">Settings</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="#" id="userIcon"><i class="fas fa-user"></i></a>

            <div class="user-dropdown" id="userDropdown">
                <form id="signupForm">
                    <h3>Sign Up</h3>
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="contact" placeholder="Contact Number" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Sign Up</button>
                    <p>Already have an account? <a href="#" id="toggleToLogin">Login</a></p>
                </form>

                <form id="loginForm" style="display: none;">
                    <h3>Login</h3>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                    <p>Don't have an account? <a href="#" id="toggleToSignup">Sign Up</a></p>
                </form>
            </div>
        <?php endif; ?>
    </div>
</header>


    <!-- NAVIGATION MENU -->
   <!-- Main Navigation -->
   <nav class="navbar">
    <ul>
        <?php
        include 'db_config.php';

        // Fetch all categories with icons from the categories table
        $categories_result = $conn->query("SELECT id, name, icon FROM icon_categories");

        while ($category_row = $categories_result->fetch_assoc()) {
            $category_id = $category_row['id'];  // Fetch category ID
            $category_name = $category_row['name'];
            $icon = $category_row['icon'];

            echo "
                <li class='nav-item'>
                    <a href='#category-$category_id'><i class='$icon'></i> $category_name</a>
                </li>
            ";
        }
        ?>
    </ul>
</nav>




<!-- Secondary Navigation -->
<nav class="secondary-nav">
    <ul>
        <li><a href="orders.php"><i class="fas fa-truck"></i> Order Status</a></li>
        <li><a href="account.php"><i class="fas fa-user-circle"></i> Accounts</a></li>
        <li><a href="favorites.php"> Saved Items
        <?php
$user_id = $_SESSION['user_id'] ?? 0;
$fav_count = $conn->query("SELECT COUNT(*) AS count FROM favorites WHERE user_id = '$user_id'")->fetch_assoc()['count'];
?>

<a href="favorites.php">
    <i class="fa fa-heart"></i>
    <span id="favorite-count"><?= $fav_count; ?></span>
</a>
</a></li>
    </ul>
</nav>



    <!-- HERO BANNER -->
    <?php
$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in
?>
<section class="hero">
    <div class="hero-content">
        <?php if ($isLoggedIn): ?>
            <h1>Welcome Back!</h1>
            <p>Continue exploring our latest collections.</p>
            <a href="shop.php" class="btn">Browse Products</a>
        <?php else: ?>
            <h1>Discover the Latest Trends</h1>
            <p>Shop top-quality fashion at unbeatable prices</p>
            <a href="#" class="btn" id="startShoppingBtn">Start Shopping</a>
        <?php endif; ?>
    </div>
</section>



<!-- PRODUCT SECTION (Dynamic) -->
<?php
include 'db_config.php';

$user_id = $_SESSION['user_id'] ?? 0; // Ensure user session is set

// Fetch all categories
$categories_result = $conn->query("SELECT id, name FROM categories");

while ($category_row = $categories_result->fetch_assoc()) {
    $category_id = $category_row['id'];
    $category_name = $category_row['name'];
?>
    <section class='products' id='category-<?php echo $category_id; ?>'> 
        <h2><?php echo htmlspecialchars($category_name); ?></h2>

        <div class='swiper mySwiper'>
            <div class='swiper-wrapper'>

                <?php
                // Fetch products for this category
                $result = $conn->query("SELECT * FROM products WHERE category_id='$category_id' LIMIT 20");

                while ($row = $result->fetch_assoc()) {
                    $image_path = !empty($row['image']) ? "uploads/{$row['image']}" : "uploads/default.jpg";
                    $product_id = $row['id'];

                    // Check if the product is in the user's favorite list
                    $fav_check = $conn->query("SELECT * FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'");
                    $is_favorited = ($fav_check->num_rows > 0);
                    $favorite_class = $is_favorited ? "fas" : "far"; // Filled if favorite, outlined if not
                ?>
                    <div class='swiper-slide product-card'>
                        <!-- Make Image Clickable -->
                        <a href="product_details.php?id=<?php echo $product_id; ?>">
                            <img src='<?php echo $image_path; ?>' alt='<?php echo htmlspecialchars($row['name']); ?>' 
                                 onerror="this.src='uploads/default.jpg';">
                        </a>

                        <!-- Make Product Name Clickable -->
                        <h3>
                            <a href="product_details.php?id=<?php echo $product_id; ?>">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </a>
                        </h3>

                        <p>GHC <?php echo $row['price']; ?></p>

                        <!-- Favorite Button -->
                        <button class="favorite-btn" data-id="<?php echo $product_id; ?>">
                            <i class="<?php echo $favorite_class; ?> fa-heart"></i>
                        </button>

                        <!-- Add to Cart -->
                        <a href='#' class='btn' onclick='addToCart(<?php echo $row['id']; ?>)'>Add to Cart</a>
                    </div>
                <?php } ?>

            </div>
        </div>

        <!-- More on Category Button -->
        <a href="more_products.php?category_id=<?php echo $category_id; ?>" class="btn">
            More on <?php echo htmlspecialchars($category_name); ?>
        </a>

    </section>
<?php } ?>


    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>We offer the best fashion products at affordable prices.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Shop</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        <p class="footer-bottom">&copy; 2025 MyShop. All rights reserved.</p>
    </footer>


<script>
  document.addEventListener("DOMContentLoaded", function () {
    updateFavoriteCount();

    document.querySelectorAll(".favorite-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            let productId = this.dataset.id;
            let icon = this.querySelector("i");

            fetch("favorites_handler.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "added") {
                    icon.classList.remove("far");
                    icon.classList.add("fas");
                } else if (data.status === "removed") {
                    icon.classList.remove("fas");
                    icon.classList.add("far");
                }
                updateFavoriteCount();
            });
        });
    });

    function updateFavoriteCount() {
        fetch("fetch_fav_count.php")
            .then(response => response.text())
            .then(count => {
                document.getElementById("favorite-count").textContent = count;
            });
    }
});


</script>

     <!-- Swiper.js Script -->
     <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 4,  // Number of products visible at a time
            spaceBetween: 20,  // Space between slides
            loop: true,  // Infinite scrolling
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                320: { slidesPerView: 1 }, // Mobile
                768: { slidesPerView: 2 }, // Tablet
                1024: { slidesPerView: 3 }, // Small screens
                1200: { slidesPerView: 4 }  // Large screens
            }
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuBtn = document.querySelector(".menu-btn");
        const navMenu = document.querySelector("nav ul");

        menuBtn.addEventListener("click", function () {
            navMenu.classList.toggle("show");
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    function promptLogin() {
        alert("Please log in or sign up to continue.");
        window.scrollTo({ top: 0, behavior: "smooth" });
        document.getElementById("userDropdown").classList.add("show"); // Open dropdown
    }

    // Check if user is logged in
    let isLoggedIn = document.body.dataset.loggedIn === "true"; 

    // Add event listeners to cart and favorite buttons
    document.querySelectorAll(".add-to-cart, .favorite-btn").forEach((btn) => {
        btn.addEventListener("click", function (event) {
            if (!isLoggedIn) {
                event.preventDefault();
                promptLogin();
            }
        });
    });

    // Hero button triggers login prompt if not logged in
    document.getElementById("startShoppingBtn")?.addEventListener("click", function (event) {
        if (!isLoggedIn) {
            event.preventDefault();
            promptLogin();
        }
    });
});

</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("loginForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default page reload

        let formData = new FormData(this);

        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                location.reload(); // Refresh page to update UI
            } else if (data.trim() === "invalid") {
                alert("Invalid password. Please try again.");
            } else if (data.trim() === "not_found") {
                alert("No account found with this email.");
            } else {
                alert("An error occurred. Please try again.");
            }
        });
    });
});

</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const userIcon = document.getElementById("userIcon");
    const userDropdown = document.getElementById("userDropdown");
    const signupForm = document.getElementById("signupForm");
    const loginForm = document.getElementById("loginForm");
    const toggleToLogin = document.getElementById("toggleToLogin");
    const toggleToSignup = document.getElementById("toggleToSignup");

    userIcon.addEventListener("click", function(event) {
        event.preventDefault();
        userDropdown.style.display = userDropdown.style.display === "block" ? "none" : "block";
    });

    toggleToLogin.addEventListener("click", function(event) {
        event.preventDefault();
        signupForm.style.display = "none";
        loginForm.style.display = "block";
    });

    toggleToSignup.addEventListener("click", function(event) {
        event.preventDefault();
        signupForm.style.display = "block";
        loginForm.style.display = "none";
    });

    // Hide dropdown when clicking outside
    document.addEventListener("click", function(event) {
        if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.style.display = "none";
        }
    });
});

</script>
<script>
    let lastScrollY = window.scrollY;
const nav = document.querySelector("nav");

window.addEventListener("scroll", () => {
    if (window.scrollY > lastScrollY) {
        // Hide the nav when scrolling down
        nav.classList.add("hidden");
    } else {
        // Show the nav when scrolling up
        nav.classList.remove("hidden");
    }
    lastScrollY = window.scrollY;
});

</script>
<script>
   document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("signupForm").addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        fetch("signup.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert(data.message);
                // Update UI to show user's name beside the user icon
                document.getElementById("userIcon").innerHTML = `<i class="fas fa-user"></i> ${data.user}`;
                document.getElementById("userDropdown").innerHTML = `
                    <p>Welcome, ${data.user}!</p>
                    <a href="settings.php">Settings</a>
                    <a href="logout.php">Logout</a>
                `;
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});

</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");

    searchInput.addEventListener("keyup", function () {
        let query = searchInput.value.trim();

        if (query.length > 1) {
            fetch("search.php", {
                method: "POST",
                body: new URLSearchParams({ query: query }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
            })
                .then((response) => response.text())
                .then((data) => {
                    searchResults.innerHTML = data;
                    searchResults.style.display = "block";
                });
        } else {
            searchResults.style.display = "none";
        }
    });

    // Hide search results when clicking outside
    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.style.display = "none";
        }
    });
});


</script>
<script>
    document.querySelectorAll(".nav a").forEach(link => {
    link.addEventListener("click", function(e) {
        e.preventDefault(); // Prevent default anchor behavior

        let targetSection = document.querySelector(this.getAttribute("href"));
        if (targetSection) {
            window.scrollTo({
                top: targetSection.offsetTop - 80, // Adjust for header height
                behavior: "smooth"
            });
        }
    });
});

</script>

</body>
</html>
