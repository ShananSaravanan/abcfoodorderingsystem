<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login_customer.php'); // Redirect to login page if not logged in
    exit();
}

$customer_id = $_SESSION['id'];

// Include the connection file
include "connection.php";

// Check if the cart is not empty
if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
    // Get the temporary address
    $deliveryAddress =  $_POST["deliveryAddress"];
    $$quantity =  $_POST["quantity"];
    // Get a random chef ID from the chef table
    $randomChefSQL = "SELECT id FROM chef ORDER BY RAND() LIMIT 1";
    $result = $conn->query($randomChefSQL);

    if ($result->num_rows > 0) {
        $chefIdRow = $result->fetch_assoc();
        $chefId = $chefIdRow['id'];

        // Insert order information into customerorder table
        $orderDate = date("Y-m-d H:i:s"); // Current date and time
        $status = "Pending";

        // Loop through each item in the cart and insert into customerorder
        foreach ($_SESSION["cart"] as $item) {
            $menuId = $item['itemId'];
            $quantity = $item['quantity'];

            $insertOrderSQL = "INSERT INTO customerorder (Customer_ID, menu_id,quantity, Order_Date, Order_Status, Chef_ID, DeliveryAddress) VALUES ('$customer_id', '$menuId','$quantity', '$orderDate', '$status', '$chefId', '$deliveryAddress')";
            
            if ($conn->query($insertOrderSQL) === TRUE) {
                // Order placed successfully, you can perform additional actions if needed
            } else {
                echo "Error: " . $insertOrderSQL . "<br>" . $conn->error;
            }
        }

        // Clear the cart and temporary address after successful checkout
        unset($_SESSION["cart"]);
        unset($_SESSION['temporaryAddress']);

        echo "Checkout successful!";
    } else {
        echo "Error: Unable to retrieve a random chef ID.";
    }

} else {
    echo "Your cart is empty. Add items to the cart before checking out.";
}

// Close the database connection
$conn->close();
?>
