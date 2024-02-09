<?php
session_start();
include 'connection.php';  // Make sure to include your database connection file

// Check if the orderId is set in the POST request
if (isset($_POST['orderId'])) {
    $orderId = $_POST['orderId'];

    // Validate and sanitize the orderId to prevent SQL injection
    $orderId = mysqli_real_escape_string($conn, $orderId);

    // Query to check if the order exists in the deliveryhistory table
    $sql = "SELECT COUNT(*) AS orderCount FROM deliveryhistory WHERE Order_ID = '$orderId'";
    $result = $conn->query($sql);

    if ($result) {
        // Fetch the result as an associative array
        $row = $result->fetch_assoc();

        // Check if the order exists (orderCount > 0)
        $orderExists = ($row['orderCount'] > 0);

        // Return the result as JSON
        echo json_encode(['exists' => $orderExists]);
    } else {
        // Handle the query error
        echo json_encode(['exists' => false, 'error' => $conn->error]);
    }
} else {
    // Handle the case where orderId is not set in the POST request
    echo json_encode(['exists' => false, 'error' => 'orderId not set']);
}

// Close the database connection
$conn->close();
?>
