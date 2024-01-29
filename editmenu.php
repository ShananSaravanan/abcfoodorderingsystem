<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Assuming your form fields are named 'menuid', 'itemName', 'itemDescription', and 'itemPrice'
        $menu_id = $_POST["menuid"];
        $item_name = $_POST["itemName"];
        $item_description = $_POST["itemDescription"];
        $item_price = $_POST["itemPrice"];

        // Update the menu in the database
        $update_query = "UPDATE menu SET Item_Name = ?, Description = ?, Price = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssi", $item_name, $item_description, $item_price, $menu_id);
        
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
