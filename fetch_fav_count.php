<?php
include 'db_config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id) {
    $result = $conn->query("SELECT COUNT(*) AS count FROM favorites WHERE user_id = '$user_id'");
    $row = $result->fetch_assoc();
    echo $row['count'];
} else {
    echo 0;
}
?>
