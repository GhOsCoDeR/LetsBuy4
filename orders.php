<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Container */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }

        /* Order Status Styling */
        .status {
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .pending {
            background: #f4b400;
            color: white;
        }

        .completed {
            background: #28a745;
            color: white;
        }

        .canceled {
            background: #dc3545;
            color: white;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #0056b3;
        }

        /* Responsive Design */
        @media screen and (max-width: 600px) {
            th, td {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Orders</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Total Price</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        
        <?php while ($row = $orders->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $row['id']; ?></td>
            <td>GHC <?php echo number_format($row['total_price'], 2); ?></td>
            <td><?php echo ucfirst($row['payment_method']); ?></td>
            <td>
    <?php if (!empty($row['status'])): ?>
        <span class="status <?php echo strtolower($row['status']); ?>">
            <?php echo ucfirst($row['status']); ?>
        </span>
    <?php else: ?>
        <span class="status pending">Pending</span> <!-- Default to Pending if empty -->
    <?php endif; ?>
</td>

            <td><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="index.php" class="back-btn">← Back to Shopping</a>
</div>

</body>
</html>
