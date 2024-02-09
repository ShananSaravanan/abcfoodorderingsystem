<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    // Update the order status to "Cancelled" in the database
    $updateSql = "UPDATE customerorder SET Order_Status = 'Cancelled' WHERE id = $orderId";
    $conn->query($updateSql);

    // Return a response (you can customize the response as needed)
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
} else {
    // Handle invalid or missing parameters
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
