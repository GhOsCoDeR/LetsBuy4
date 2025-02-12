document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function () {
            let productId = this.getAttribute("data-id");

            fetch("add_to_cart.php", {
                method: "POST",
                body: new URLSearchParams({ product_id: productId }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert(data.message);
                    document.getElementById("cart-count").textContent = data.cart_count; // Update cart count
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
});




