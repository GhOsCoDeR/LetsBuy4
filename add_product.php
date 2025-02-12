<?php
include 'db_config.php'; // Database connection

// Fetch categories for dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .color-image-group { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .color-input, .image-input { flex: 1; }
        .remove-btn { background: red; color: white; padding: 5px; cursor: pointer; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <form action="process_add_product.php" method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="product_name" required>

            <label>Category:</label>
            <select name="category_id" id="categorySelect" required>
                <option value="">Select Category</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label>Subcategory:</label>
            <select name="subcategory_id" id="subcategorySelect" required>
                <option value="">Select Subcategory</option>
            </select>

            <label>Price (GHC):</label>
            <input type="number" name="price" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Product URL:</label>
            <input type="url" name="product_url" placeholder="Enter product link from supplier" required>

            <label>Main Product Image:</label>
            <input type="file" name="product_image" required>

            <label>Variations (Optional):</label>
            <div id="colorImageContainer">
                <div class="color-image-group">
                    <input type="text" name="colors[]" class="color-input" placeholder="Color (e.g., Red)">
                    <input type="file" name="images[]" class="image-input">
                    <button type="button" class="remove-btn" onclick="removeColorImage(this)">X</button>
                </div>
            </div>
            <button type="button" onclick="addColorImage()">+ Add More</button>

            <button type="submit">Add Product</button>
        </form>

        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

<script>
function addColorImage() {
    const container = document.getElementById("colorImageContainer");
    const div = document.createElement("div");
    div.className = "color-image-group";
    div.innerHTML = `
        <input type="text" name="colors[]" class="color-input" placeholder="Color (e.g., Red)">
        <input type="file" name="images[]" class="image-input">
        <button type="button" class="remove-btn" onclick="removeColorImage(this)">X</button>
    `;
    container.appendChild(div);
}

function removeColorImage(btn) {
    btn.parentElement.remove();
}

document.getElementById("categorySelect").addEventListener("change", function() {
    var categoryId = this.value;
    var subcategoryDropdown = document.getElementById("subcategorySelect");

    subcategoryDropdown.innerHTML = "<option value=''>Loading...</option>";

    if (categoryId) {
        fetch("fetch_subcategories.php?category_id=" + categoryId)
        .then(response => response.json())
        .then(data => {
            subcategoryDropdown.innerHTML = "<option value=''>Select Subcategory</option>";
            data.forEach(subcategory => {
                subcategoryDropdown.innerHTML += `<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`;
            });
        })
        .catch(error => {
            console.error("Error fetching subcategories:", error);
            subcategoryDropdown.innerHTML = "<option value=''>Error loading subcategories</option>";
        });
    } else {
        subcategoryDropdown.innerHTML = "<option value=''>Select Category First</option>";
    }
});
</script>

</body>
</html>
