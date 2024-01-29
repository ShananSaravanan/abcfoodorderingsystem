<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["menuid"])) {
    try {
        $menu_id = $_GET["menuid"];

        // Delete the menu from the database
        $delete_query = "DELETE FROM menu WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $menu_id);

        if ($stmt->execute()) {
            echo "<script>alert('Menu deleted successfully!'); window.location.href='vendor_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting menu. Please try again.'); window.location.href='vendor_dashboard.php';</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "<script>alert('Error: $e'); window.location.href='vendor_dashboard.php';</script>";
    }
}
?>
