<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Assuming your form fields are named 'menuid', 'itemName', 'itemDescription', 'itemPrice', and 'editItemImage'
        $menu_id = $_POST["menuid"];
        $item_name = $_POST["itemName"];
        $item_description = $_POST["itemDescription"];
        $item_price = $_POST["itemPrice"];

        // File handling for image upload
        $menuImagePath = null;
        if (isset($_FILES['editItemImage']) && $_FILES['editItemImage']['error'] === 0) {
            $uploadDir = 'images/';
            $uploadPath = $uploadDir . basename($_FILES['editItemImage']['name']);
            
            if (move_uploaded_file($_FILES['editItemImage']['tmp_name'], $uploadPath)) {
                $menuImagePath = $uploadPath;
            } else {
                // Handle file upload error
                echo "<script>alert('File upload failed.'); window.location.href='vendor_dashboard.php';</script>";
                exit();
            }
        }

        // Update the menu in the database
        $update_query = "UPDATE menu SET Item_Name = ?, Item_Description = ?, Price = ?, menu_img = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssi", $item_name, $item_description, $item_price, $menuImagePath, $menu_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Menu updated successfully!'); window.location.href='vendor_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating menu. Please try again.'); window.location.href='vendor_dashboard.php';</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "<script>alert('Error: $e'); window.location.href='vendor_dashboard.php';</script>";
    }
}
?>
