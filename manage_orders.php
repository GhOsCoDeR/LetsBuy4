<?php
session_start();
include 'db_config.php';

// Fetch all orders
$orders = $conn->query("SELECT id, billing_firstname, billing_lastname, total_price, payment_method, status, created_at 
                        FROM orders 
                        ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 100%;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .order-table {
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .status-select {
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
        }

        .update-btn {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .update-btn:hover {
            background: #218838;
        }

        .scrollable-table {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Orders</h2>
    <div class="scrollable-table">
        <table class="order-table">
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['billing_firstname'] . " " . $row['billing_lastname']); ?></td>
                <td>GHC <?php echo number_format($row['total_price'], 2); ?></td>
                <td><?php echo ucfirst($row['payment_method']); ?></td>
                <td>
                    <select class="status-select" data-id="<?php echo $row['id']; ?>">
                        <option value="Processing" <?php echo ($row['status'] == "Processing") ? "selected" : ""; ?>>Processing</option>
                        <option value="Shipped" <?php echo ($row['status'] == "Shipped") ? "selected" : ""; ?>>Shipped</option>
                        <option value="Delivered" <?php echo ($row['status'] == "Delivered") ? "selected" : ""; ?>>Delivered</option>
                    </select>
                </td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <button class="update-btn" onclick="updateStatus(<?php echo $row['id']; ?>)">Update</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
function updateStatus(orderId) {
    const statusSelect = document.querySelector(`.status-select[data-id='${orderId}']`);
    const status = statusSelect.value;

    fetch("update_order_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `order_id=${orderId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Order status updated successfully!");
            statusSelect.classList.add("updated"); // Optional: Add a visual indicator
        } else {
            alert("Failed to update order status.");
        }
    })
    .catch(error => console.error("Error:", error));
}

</script>

</body>
</html>
