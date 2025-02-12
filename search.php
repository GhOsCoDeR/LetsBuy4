<?php
require 'db_config.php'; // Include your database connection

if (isset($_POST['query'])) {
    $searchTerm = "%" . trim($_POST['query']) . "%"; // Add wildcards for partial matching

    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR category LIKE ?");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul class='search-list'>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='product_page.php?id=" . $row['id'] . "'>" . $row['name'] . " (" . $row['category'] . ")</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No products found.</p>";
    }
}
?>
