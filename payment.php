<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <style>
        .checkout-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .section {
            margin-bottom: 20px;
            text-align: left;
        }

        h3 {
            margin-bottom: 10px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            background: #28a745;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background: #218838;
        }

        .btn-secondary {
            background: #007bff;
        }

        .btn-secondary:hover {
            background: #0056b3;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <h2>Checkout</h2>

    <form action="process_payment.php" method="POST">

        <!-- Billing & Shipping Section -->
        <div id="billingSection">
            <div class="section">
                <h3>Billing Address</h3>
                <label>First Name</label>
                <input type="text" name="billing_firstname" required>

                <label>Last Name</label>
                <input type="text" name="billing_lastname" required>

                <label>Contact</label>
                <input type="text" name="billing_contact" required>

                <label>Street</label>
                <input type="text" name="billing_street" required>

                <label>City</label>
                <input type="text" name="billing_city" required>

                <label>Country</label>
                <select name="billing_country" class="country-dropdown" required></select>
            </div>

            <div class="section">
                <h3>Shipping Address <span>(Optional)</span></h3>
                <input type="checkbox" id="sameAsBilling">
                <label for="sameAsBilling">Same as Billing Address</label>

                <div id="shippingFields">
                    <label>First Name</label>
                    <input type="text" name="shipping_firstname">

                    <label>Last Name</label>
                    <input type="text" name="shipping_lastname">

                    <label>Street</label>
                    <input type="text" name="shipping_street">

                    <label>City</label>
                    <input type="text" name="shipping_city">

                    <label>Country</label>
                    <select name="shipping_country" class="country-dropdown"></select>
                </div>
            </div>

            <button type="button" class="btn" id="nextToPayment">Continue to Payment Method</button>
        </div>

        <!-- Payment Method Section -->
        <div id="paymentSection" class="hidden">
            <div class="section">
                <h3>Payment Method</h3>
                <label><input type="radio" name="payment_method" value="credit_card" checked> Credit/Debit Card</label>
                <label><input type="radio" name="payment_method" value="mobile_money"> Mobile Money</label>

                <div id="creditCardFields">
                    <label>Card Number</label>
                    <input type="text" name="card_number" required>

                    <label>Expiration Date</label>
                    <input type="month" name="card_expiry" required>

                    <label>CVV</label>
                    <input type="text" name="card_cvv" required>
                </div>

                <div id="mobileMoneyFields" style="display: none;">
                    <label>Select Network</label>
                    <select name="mobile_money_network">
                        <option value="mtn">MTN Mobile Money</option>
                        <option value="telecel">Telecel Cash</option>
                        <option value="airteltigo">Airtel Tigo Cash</option>
                    </select>

                    <label>Mobile Number</label>
                    <input type="text" name="mobile_money_number">
                </div>
            </div>

            <button type="button" class="btn btn-secondary" id="backToBilling">Back to Billing Information</button>
            <button type="submit" class="btn">Confirm Payment</button>
        </div>
    </form>
</div>

<script>
// Step-based Navigation
document.getElementById("nextToPayment").addEventListener("click", function() {
    document.getElementById("billingSection").classList.add("hidden");
    document.getElementById("paymentSection").classList.remove("hidden");
});

document.getElementById("backToBilling").addEventListener("click", function() {
    document.getElementById("paymentSection").classList.add("hidden");
    document.getElementById("billingSection").classList.remove("hidden");
});

// Toggle Shipping Fields
document.getElementById("sameAsBilling").addEventListener("change", function() {
    document.getElementById("shippingFields").style.display = this.checked ? "none" : "block";
});

// Toggle Payment Fields
document.querySelectorAll('input[name="payment_method"]').forEach((elem) => {
    elem.addEventListener("change", function() {
        document.getElementById("creditCardFields").style.display = (this.value === "credit_card") ? "block" : "none";
        document.getElementById("mobileMoneyFields").style.display = (this.value === "mobile_money") ? "block" : "none";
    });
});

// Populate Country Dropdown & Sort Alphabetically
fetch('https://restcountries.com/v3.1/all')
    .then(response => response.json())
    .then(data => {
        let sortedCountries = data.map(country => country.name.common).sort();
        let countryOptions = sortedCountries.map(name => `<option value="${name}">${name}</option>`).join("");

        document.querySelectorAll(".country-dropdown").forEach(select => select.innerHTML = countryOptions);
        $(".country-dropdown").select2({ placeholder: "Select a country" });
    })
    .catch(error => console.error("Error loading countries:", error));
</script>

</body>
</html>
