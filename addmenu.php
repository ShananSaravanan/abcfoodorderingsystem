<?php
session_start(); // Start the session

include 'connection.php';

// Check if the vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header('Location: login_vendor.php'); // Redirect to login page if not logged in
    exit();
}

// Get vendor ID from the session
$vendor_id = $_SESSION['vendor_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $itemName = $_POST['newItemName'] ?? '';
    $itemDescription = $_POST['newItemDescription'] ?? '';
    $itemPrice = $_POST['newItemPrice'] ?? '';
    
    // File handling for image upload
    $menuImagePath = null;
    if (isset($_FILES['newItemImage']) && $_FILES['newItemImage']['error'] === 0) {
        $uploadDir = 'images/';
        $uploadPath = $uploadDir . basename($_FILES['newItemImage']['name']);
        
        if (move_uploaded_file($_FILES['newItemImage']['tmp_name'], $uploadPath)) {
            $menuImagePath = $uploadPath;
        } else {
            // Handle file upload error
            echo "File upload failed.";
            exit();
        }
    }
    
    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO menu (vendor_id, Item_Name, Item_Description, Price, menu_img) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $vendor_id, $itemName, $itemDescription, $itemPrice, $menuImagePath);
    if ($stmt->execute()) {
        // Success, redirect to the vendor dashboard or any other page
        header('Location: vendor_dashboard.php');
        exit();
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
