<?php
include "db_config.php";

// Fetch total sales per month
$salesQuery = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_price) AS total_sales
    FROM orders 
    GROUP BY month
    ORDER BY month ASC
");

$salesData = [];
while ($row = $salesQuery->fetch_assoc()) {
    $salesData['months'][] = $row['month'];
    $salesData['sales'][] = $row['total_sales'];
}

// Fetch new users per month
$usersQuery = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS user_count
    FROM users
    GROUP BY month
    ORDER BY month ASC
");

$usersData = [];
while ($row = $usersQuery->fetch_assoc()) {
    $usersData['months'][] = $row['month'];
    $usersData['users'][] = $row['user_count'];
}

// Fetch top-selling products
$productsQuery = $conn->query("
    SELECT products.name, SUM(order_items.quantity) AS total_sold 
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    GROUP BY products.name
    ORDER BY total_sold DESC
    LIMIT 5
");

$productsData = [];
while ($row = $productsQuery->fetch_assoc()) {
    $productsData['products'][] = $row['name'];
    $productsData['sales'][] = $row['total_sold'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .chart-container h2 {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage.php"><i class="fas fa-box"></i> Manage Products</a></li>
            <li><a href="#"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="analytics.php" class="active"><i class="fas fa-chart-bar"></i> Analytics</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h1>Analytics</h1>
        </header>

        <div class="chart-container">
            <h2>Sales Trends</h2>
            <canvas id="salesChart"></canvas>
        </div>

        <div class="chart-container">
            <h2>New Users Per Month</h2>
            <canvas id="usersChart"></canvas>
        </div>

        <div class="chart-container">
            <h2>Top-Selling Products</h2>
            <canvas id="productsChart"></canvas>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Sales Chart
            const salesCtx = document.getElementById("salesChart").getContext("2d");
            new Chart(salesCtx, {
                type: "line",
                data: {
                    labels: <?= json_encode($salesData['months'] ?? []) ?>,
                    datasets: [{
                        label: "Total Sales (GHC)",
                        data: <?= json_encode($salesData['sales'] ?? []) ?>,
                        borderColor: "#3498db",
                        backgroundColor: "rgba(52, 152, 219, 0.2)",
                        fill: true,
                    }]
                }
            });

            // Users Chart
            const usersCtx = document.getElementById("usersChart").getContext("2d");
            new Chart(usersCtx, {
                type: "bar",
                data: {
                    labels: <?= json_encode($usersData['months'] ?? []) ?>,
                    datasets: [{
                        label: "New Users",
                        data: <?= json_encode($usersData['users'] ?? []) ?>,
                        backgroundColor: "#2ecc71"
                    }]
                }
            });

            // Products Chart
            const productsCtx = document.getElementById("productsChart").getContext("2d");
            new Chart(productsCtx, {
                type: "pie",
                data: {
                    labels: <?= json_encode($productsData['products'] ?? []) ?>,
                    datasets: [{
                        data: <?= json_encode($productsData['sales'] ?? []) ?>,
                        backgroundColor: ["#e74c3c", "#f1c40f", "#8e44ad", "#3498db", "#2ecc71"]
                    }]
                }
            });
        });
    </script>

</body>
</html>
