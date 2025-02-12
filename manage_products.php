<?php
include 'db_config.php';

// Fetch all active products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external stylesheet -->
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #2C3E50;
            color: white;
            padding: 20px;
            position: fixed;
            transition: 0.3s;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            padding: 12px;
            transition: 0.3s;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background: #34495E;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
            transition: 0.3s;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        th {
            background: #2C3E50;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        /* Product Image */
        td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Buttons */
        .btn {
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            display: inline-block;
            transition: 0.3s;
            font-size: 14px;
            margin: 3px;
        }

        .edit {
            background-color: #3498db;
        }

        .delete {
            background-color: #e74c3c;
        }

        .view-url {
            background-color: #27ae60;
            font-size: 12px;
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Search Bar */
        .search-container {
            margin: 20px 0;
            text-align: right;
        }

        #searchInput {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
        }

        #searchInput:focus {
            border-color: #3498db;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }

            th, td {
                font-size: 14px;
            }

            td img {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="deleted_products.php">Deleted Products</a></li>
            <li><a href="order_management.php">Orders</a></li>
            <li><a href="analytics.php">Analytics</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header>
            <h1>Manage Products</h1>
        </header>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search products...">
        </div>

        <table id="productTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Source</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productBody">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><img src="uploads/<?= $row['image'] ?>" width="50"></td>
                        <td title="<?= $row['name'] ?>"><?= strlen($row['name']) > 25 ? substr($row['name'], 0, 22) . "..." : $row['name']; ?></td>
                        <td>GHC <?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['category'] ?></td>
                        <td>
                            <?php if (!empty($row['product_url'])): ?>
                                <a href="<?= $row['product_url'] ?>" target="_blank" class="btn view-url">View</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
                            <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let searchValue = this.value.toLowerCase();
    let rows = document.getElementById("productBody").getElementsByTagName("tr");

    for (let row of rows) {
        let productName = row.cells[2].innerText.toLowerCase();
        let productCategory = row.cells[4].innerText.toLowerCase();

        row.style.display = (productName.includes(searchValue) || productCategory.includes(searchValue)) ? "" : "none";
    }
});
</script>

</body>
</html>
