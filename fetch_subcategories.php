<?php
include 'db_config.php';

if (isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']); // Sanitize input

    $query = "SELECT id, subcategory_name FROM subcategories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $subcategories = [];
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
    }

    echo json_encode($subcategories);
}
?>
