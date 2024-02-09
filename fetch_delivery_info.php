<?php
session_start();
include 'connection.php';

// Check if the order_id is provided through POST
if (!isset($_POST['order_id'])) {
    echo 'Invalid request';
    exit();
}

$order_id = $_POST['order_id'];

// Fetch delivery information from delivery_history
$sql = "SELECT dh.Order_ID, dh.Delivery_Date, dp.Personnel_Name, dp.Contact_Information AS Personnel_Contact, dp.Delivery_VehicleInformation
        FROM deliveryhistory dh
        LEFT JOIN deliverypersonnel dp ON dh.Personnel_ID = dp.id
        WHERE dh.Order_ID = $order_id";

$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    $deliveryInfo = $result->fetch_assoc();

    // Display the fetched data in a formatted way (you can customize this based on your needs)
    echo '
          <div class="modal-body">
            <p><strong>Order ID:</strong> ' . $deliveryInfo['Order_ID'] . '</p>
            <p><strong>Delivery Date:</strong> ' . $deliveryInfo['Delivery_Date'] . '</p>
            <p><strong>Personnel Name:</strong> ' . $deliveryInfo['Personnel_Name'] . '</p>
            <p><strong>Contact Information:</strong> ' . $deliveryInfo['Personnel_Contact'] . '</p>
            <p><strong>Delivery Vehicle Information:</strong> ' . $deliveryInfo['Delivery_VehicleInformation'] . '</p>
          </div>';
} else {
    echo 'Error fetching delivery information';
}

$conn->close();
?>
