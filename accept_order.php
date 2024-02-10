<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input parameters
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : null;

    if ($orderId) {
        // Check if the order ID exists in the deliveryhistory table
        $checkSql = "SELECT * FROM deliveryhistory WHERE Order_ID = $orderId";
        $result = $conn->query($checkSql);

        if ($result && $result->num_rows > 0) {
            // If the order ID exists, update the delivery status to 'In-Transit'
            $updateSql = "UPDATE deliveryhistory SET Delivery_Status = 'In-Transit', Delivery_Date = NOW() WHERE Order_ID = $orderId";
            $conn->query($updateSql);
            
            // Output a success message
            echo 'Order status updated to In-Transit successfully.';
        } else {
            // If the order ID doesn't exist, insert a new record in the deliveryhistory table
            $personnelId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

            if ($personnelId) {
                $insertSql = "INSERT INTO deliveryhistory (Order_ID, Delivery_Status,Delivery_Date, Personnel_ID) VALUES ($orderId, 'In-Transit',Delivery_Date = NOW(), $personnelId)";
                $conn->query($insertSql);
                
                // Output a success message
                echo 'Order status set to In-Transit successfully.';
            } else {
                // Output an error message if session ID is not set
                http_response_code(500);
                echo 'Error updating order status. Session ID not set.';
            }
        }
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

// Close the database connection
$conn->close();
?>
