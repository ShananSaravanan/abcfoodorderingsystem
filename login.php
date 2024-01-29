<?php
session_start(); // Start the session

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $email = $_POST["loginEmail"];
        $password = $_POST["loginPassword"];

        // Assuming your table for login has columns 'email' and 'password'
        $table = $_POST["tablename"];
        $page = $_POST["redirect_page"];
        $frompage = $_POST["from_page"];

        // Check if the user exists in the database
        $result = $conn->query("SELECT id, password FROM $table WHERE email = '$email' LIMIT 1");

        if ($result->num_rows > 0) {
            // User found, verify password
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Password is correct, login successful
                $_SESSION['vendor_id'] = $row['id']; // Store the vendor ID in a session variable
                echo "<script>alert('Login successful!'); window.location.href='$page';</script>";
            } else {
                // Incorrect password
                echo "<script>alert('Incorrect password. Please try again.'); window.location.href='$frompage';</script>";
            }
        } else {
            // User not found
            echo "<script>alert('User not found. Please register.'); window.location.href='$frompage';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error: $e'); window.location.href='index.php';</script>";
    }
}
?>
