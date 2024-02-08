<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Log the entire $_POST array
    // ...

    // Use isset() to check if keys exist in the $_POST array
    if (isset($_POST["itemId"]) && isset($_POST["quantity"]) && isset($_POST["vendorName"]) && isset($_POST["itemName"]) && isset($_POST["deliveryInfo"])) {
        $itemId = $_POST["itemId"];
        $quantity = $_POST["quantity"];
        $vendorName = $_POST["vendorName"];
        $itemName = $_POST["itemName"];
        $deliveryInfo = $_POST["deliveryInfo"];
        $price = $_POST["price"];
        $totalprice = $_POST["totalprice"];
        // Initialize the cart if not already set
        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        // Check if the item is already in the cart
        if (isset($_SESSION["cart"][$itemId])) {
            // Update quantity if the item is already in the cart
            $_SESSION["cart"][$itemId]["quantity"] += $quantity;
        } else {
            // Add a new item to the cart
            $_SESSION["cart"][$itemId] = [
                "itemId" => $itemId,
                "quantity" => $quantity,
                "vendorName" => $vendorName,
                "itemName" => $itemName,
                "deliveryInfo" => $deliveryInfo,
                "price" => $price,
                "totalprice" => $totalprice
                // Add any other necessary details
            ];
        }

        // Get the current cart contents
        $cartContents = $_SESSION["cart"];

        // Send a response indicating success along with cart contents
        echo json_encode(["success" => true, "cartContents" => $cartContents]);
        exit;
    } else {
        // Use file_get_contents to get the raw POST data
        $postData = json_decode(file_get_contents("php://input"), true);

        // Add this line to log the received data
        error_log("Received POST data: " . json_encode($postData));

        // Use $postData to get itemId, quantity, vendorName, itemName, and deliveryInfo
        $itemId = isset($postData['itemId']) ? $postData['itemId'] : null;
        $quantity = isset($postData['quantity']) ? $postData['quantity'] : null;
        $vendorName = isset($postData['vendorName']) ? $postData['vendorName'] : null;
        $itemName = isset($postData['itemName']) ? $postData['itemName'] : null;
        $deliveryInfo = isset($postData['deliveryInfo']) ? $postData['deliveryInfo'] : null;

        // Log an error if required parameters are not set
        error_log("Missing parameters: itemId, quantity, vendorName, itemName, or deliveryInfo");
        echo json_encode(["success" => false, "message" => "Failed to add item to cart. Additional details: Missing parameters: itemId, quantity, vendorName, itemName, or deliveryInfo"]);
        exit;
    }
} else {
    // Log an error if the request method is not POST
    error_log("Invalid request method: " . $_SERVER["REQUEST_METHOD"]);
    error_log("Received POST data: " . json_encode($_POST));
    // Send a response indicating failure
    echo json_encode(["success" => false, "message" => "Failed to add item to cart. Additional details: Invalid request method"]);
    exit;
}
?>
