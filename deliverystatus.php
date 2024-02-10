<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input parameters
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : null;
    $newStatus = isset($_POST['newStatus']) ? $_POST['newStatus'] : null;

    if ($newStatus == "Out-For Delivery") {
        // Update the order status in the database
        $updateSql = "UPDATE customerorder SET Order_Status = 'Completed' WHERE id = $orderId";
        $conn->query($updateSql);
        $updateSql2 = "UPDATE deliveryhistory SET Delivery_Status = '$newStatus' WHERE Order_ID = $orderId";
        $result = $conn->query($updateSql2);

if (!$result) {
    echo 'Error updating database: ' . $conn->error;
    // You may also want to log the error for future reference
    // error_log('Error updating database: ' . $conn->error);
}
else{
    echo 'Order status updated successfully.';
}

        // Output a success message
        
    } 
    else if($newStatus == "Cancelled"){
        $updateSql = "UPDATE customerorder SET Order_Status = 'Waiting For Pickup' WHERE id = $orderId";
        $conn->query($updateSql);
        $updateSql = "UPDATE deliveryhistory SET Delivery_Status = '$newStatus' WHERE Order_ID = $orderId";
        $conn->query($updateSql);

        // Output a success message
        echo 'Order status updated successfully.';
    }
    else if($newStatus == "Delivered"){
        $updateSql = "UPDATE deliveryhistory SET Delivery_Status = '$newStatus' WHERE Order_ID = $orderId";
        $conn->query($updateSql);

        // Output a success message
        echo 'Order status updated successfully.';
    }
    else {
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
