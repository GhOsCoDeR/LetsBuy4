
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>


<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include "db_config.php";



// Fetch total counts
$productCount = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$orderCount = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

// Fetch recent orders
$recentOrders = $conn->query("SELECT id, billing_firstname, status, total_price FROM orders ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="dashboard.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
</head>
<body>


    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_products.php"><i class="fas fa-box"></i> Manage Products</a></li>
            <li><a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a></li>
            <li><a href="logout.php" class="btn logout">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h1>Dashboard</h1>
            <div class="theme-toggle">
                <label class="switch">
                    <input type="checkbox" id="themeSwitcher">
                    <span class="slider round"></span>
                </label>
            </div>
        </header>

        <section class="stats">
            <div class="card"><i class="fas fa-box"></i> <span>Total Products:</span> <?php echo $productCount; ?></div>
            <div class="card"><i class="fas fa-shopping-cart"></i> <span>Total Orders:</span> <?php echo $orderCount; ?></div>
            <div class="card"><i class="fas fa-users"></i> <span>Total Users:</span> <?php echo $userCount; ?></div>
        </section>

        <section class="dashboard-content">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recentOrders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo $row['billing_firstname']; ?></td>
                            <td class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></td>
                            <td>GHC <?php echo number_format($row['total_price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const themeSwitcher = document.getElementById("themeSwitcher");
    const body = document.body;

    if (localStorage.getItem("theme") === "dark") {
        body.classList.add("dark-mode");
        themeSwitcher.checked = true;
    }

    themeSwitcher.addEventListener("change", function () {
        if (themeSwitcher.checked) {
            body.classList.add("dark-mode");
            localStorage.setItem("theme", "dark");
        } else {
            body.classList.remove("dark-mode");
            localStorage.setItem("theme", "light");
        }
    });
});

    </script>
</body>
</html>
