<?php
session_start();
include 'db_config.php'; // Database connection

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch admin credentials from the database
    $query = "SELECT * FROM admin WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='admin_login.php';</script>";
        }
    } else {
        echo "<script>alert('Admin not found!'); window.location.href='admin_login.php';</script>";
    }
}
?>
