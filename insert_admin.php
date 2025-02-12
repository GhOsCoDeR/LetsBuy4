<?php
include 'db_config.php';

$email = "admin23@gmail.com";
$password = "adams";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO admin (email, password) VALUES ('$email', '$hashed_password')";

if (mysqli_query($conn, $query)) {
    echo "Admin inserted successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
