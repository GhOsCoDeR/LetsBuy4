<?php
session_start(); // Start session
require 'db_config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = trim($_POST['password']);

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists."]);
        exit();
    }

    // Hash password before storing
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, contact, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullName, $email, $contact, $hashedPassword);

    if ($stmt->execute()) {
        // Automatically log in user after signup
        $_SESSION['user_id'] = $stmt->insert_id; // Get last inserted ID
        $_SESSION['full_name'] = $fullName;
        
        echo json_encode(["status" => "success", "message" => "Account created successfully!", "user" => $fullName]);
    } else {
        echo json_encode(["status" => "error", "message" => "Signup failed. Please try again."]);
    }
}
?>
