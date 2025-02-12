<?php session_start(); ?>
<?php if (!isset($_SESSION['user_id'])) header("Location: index.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dark-mode {
    background: #222;
    color: #fff;
}

    </style>
</head>
<body>
    <h2>Settings</h2>

    <label>
        <input type="checkbox" id="darkModeToggle">
        Enable Dark Mode
    </label>

    <script>
        document.getElementById("darkModeToggle").addEventListener("change", function() {
            document.body.classList.toggle("dark-mode", this.checked);
            localStorage.setItem("darkMode", this.checked);
        });

        if (localStorage.getItem("darkMode") === "true") {
            document.body.classList.add("dark-mode");
            document.getElementById("darkModeToggle").checked = true;
        }
    </script>
</body>
</html>
