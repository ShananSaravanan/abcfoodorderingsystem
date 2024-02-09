<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input parameters
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : null;
    $newStatus = isset($_POST['newStatus']) ? $_POST['newStatus'] : null;

    if ($orderId && $newStatus) {
        // Update the order status in the database
        $updateSql = "UPDATE customerorder SET Order_Status = '$newStatus' WHERE id = $orderId";
        $conn->query($updateSql);

        // Output a success message
        echo 'Order status updated successfully.';
    } else {
        // Output an error message if parameters are missing
        http_response_code(400);
        echo 'Invalid input parameters.';
    }
} else {
    // Output an error message for invalid request method
    http_response_code(405);
    echo 'Invalid request method.';
}
?>
