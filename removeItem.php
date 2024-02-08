<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Use isset() to check if keys exist in the $_POST array
    if (isset($_POST["itemId"])) {
        $itemId = $_POST["itemId"];

        // Check if the item is in the cart
        if (isset($_SESSION["cart"][$itemId])) {
            // Remove the item from the cart
            unset($_SESSION["cart"][$itemId]);

            // Send a response indicating success along with updated cart contents
            echo json_encode(["success" => true, "cartContents" => $_SESSION["cart"]]);
            exit;
        } else {
            // Send a response indicating failure if the item is not in the cart
            echo json_encode(["success" => false, "message" => "Item not found in the cart"]);
            exit;
        }
    } else {
        // Send a response indicating failure if the itemId is not set
        echo json_encode(["success" => false, "message" => "Missing parameter: itemId"]);
        exit;
    }
} else {
    // Send a response indicating failure if the request method is not POST
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}
?>
