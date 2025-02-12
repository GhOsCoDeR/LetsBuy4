<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="admin_auth.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" required>
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
