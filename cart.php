<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$query = "SELECT cart.id AS cart_id, products.id AS product_id, products.name, products.image, products.price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id = '$user_id'";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Cart Container */
        .cart-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #222;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* Table Styling */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
        }

        .cart-table th, .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .cart-table th {
            background: #2C3E50;
            color: white;
        }

        .cart-table td {
            background: #fff;
        }

        /* Product Name Truncate */
        .product-name {
            display: inline-block;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: bold;
        }

        /* Product Image */
        .cart-table img {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
        }

        .product {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        /* Quantity Controls */
        .quantity-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .quantity-btn {
            padding: 5px 10px;
            border: none;
            background: #3498db;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 3px;
        }

        .quantity-btn:hover {
            background: #217dbb;
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px;
        }

        /* Remove Button */
        .remove-btn {
            background: #ff4d4d;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .remove-btn:hover {
            background: #cc0000;
        }

        /* Cart Summary */
        .cart-summary {
            margin-top: 20px;
            font-size: 18px;
        }

        .checkout-btn {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .checkout-btn:hover {
            background: #218838;
        }

        .empty-cart {
            font-size: 18px;
            color: #777;
        }

        .shop-more {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .shop-more:hover {
            text-decoration: underline;
        }
        /* Header Styling */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #007bff; /* Primary Blue */
    color: white;
    padding: 15px 30px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

/* Logo Styling */
.logo {
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 1px;
}

/* Continue Shopping Button */
.back-link {
    display: inline-block;
    padding: 12px 20px;
    background-color: #ffffff; /* White button */
    color: #007bff;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease-in-out;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.back-link:hover {
    background-color: #f1f1f1;
    transform: translateY(-3px);
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
}

.back-link:active {
    transform: translateY(1px);
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

/* Responsive Header */
@media (max-width: 768px) {
    header {
        flex-direction: column;
        text-align: center;
    }

    .back-link {
        margin-top: 10px;
        width: 100%;
        text-align: center;
    }
}

    </style>
</head>
<body>

<header>
    <div class="logo">MyShop</div>
    <a href="index.php" class="back-link">← Continue Shopping</a>
</header>

<div class="cart-container">
    <h2>Your Shopping Cart</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_price = 0;
                while ($row = $result->fetch_assoc()):
                    $subtotal = $row['price'] * $row['quantity'];
                    $total_price += $subtotal;
                ?>
                    <tr>
                        <td class="product">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="Product Image">
                            <span class="product-name" title="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></span>
                        </td>
                        <td>GHC <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <div class="quantity-container">
                                <button class="quantity-btn decrease" data-id="<?php echo $row['cart_id']; ?>">-</button>
                                <input type="text" class="quantity-input" value="<?php echo $row['quantity']; ?>" readonly>
                                <button class="quantity-btn increase" data-id="<?php echo $row['cart_id']; ?>">+</button>
                            </div>
                        </td>
                        <td>GHC <?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Total: GHC <?php echo number_format($total_price, 2); ?></h3>
            <a href="payment.php" class="checkout-btn">Proceed to Payment</a>
        </div>
    <?php else: ?>
        <p class="empty-cart">Your cart is empty 😔</p>
        <a href="index.php" class="shop-more">Shop More</a>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', function () {
        let cartId = this.getAttribute('data-id');
        let isIncrease = this.classList.contains('increase');
        let inputField = this.parentElement.querySelector('.quantity-input');

        fetch("update_cart_quantity.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `cart_id=${cartId}&action=${isIncrease ? 'increase' : 'decrease'}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                inputField.value = data.new_quantity;
                location.reload(); // Refresh the page to update totals
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>


</body>
</html>
