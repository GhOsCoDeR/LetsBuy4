<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['order_id'], $_POST['status'])) {
        $order_id = intval($_POST['order_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Update the order status in the database
        $query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "new_status" => $status]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
    }
}
?>
