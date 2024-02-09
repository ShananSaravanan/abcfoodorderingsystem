<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login_customer.php'); // Redirect to login page if not logged in
    exit();
}

$customer_id = $_SESSION['id'];

// Function to get customer information based on customer_id
function getCustomerInfo($customer_id)
{
    include "connection.php";

    // Fetch customer information
    $sql = "SELECT id, Cust_Name, Cust_contact, personaladdress FROM customer WHERE id = $customer_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $customerInfo = $result->fetch_assoc();
        return $customerInfo;
    } else {
        return null;
    }

}

$customerInfo = getCustomerInfo($customer_id);

// Function to calculate the total price for an item
function calculateTotalPrice($quantity, $price)
{
    return $quantity * $price;
}

// Function to calculate the grand total for all items in the cart
function calculateGrandTotal($cart)
{
    $grandTotal = 0;

    foreach ($cart as $item) {
        $grandTotal += calculateTotalPrice($item['quantity'], $item['price']);
    }

    return $grandTotal;
}

// Function to display cart information grouped by vendor name
function displayCartByVendor($cart)
{
    $vendors = [];

    foreach ($cart as $itemId => $item) {
        $vendorName = $item['vendorName'];

        if (!isset($vendors[$vendorName])) {
            $vendors[$vendorName] = [];
        }

        $vendors[$vendorName][] = [
            'itemName' => $item['itemName'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'deliveryInfo' => $item['deliveryInfo'],
            'totalPrice' => calculateTotalPrice($item['quantity'], $item['price']),
            'itemId' => $itemId,
        ];
    }

    foreach ($vendors as $vendorName => $items) {
        echo "<div class='vendor-container'>";
        echo "<h4>{$vendorName}</h4>";
        echo "<table class='table'>";
        echo "<thead><tr><th>Item Name</th><th>Quantity</th><th>Price</th><th>Delivery Info</th><th>Total Price</th><th>Action</th></tr></thead>";
        echo "<tbody>";

        foreach ($items as $item) {
            echo "<tr>";
            echo "<td>{$item['itemName']}</td>";
            echo "<td id='qtyfield'>{$item['quantity']}</td>";
            echo "<td>RM{$item['price']}</td>";
            echo "<td>{$item['deliveryInfo']}</td>";
            echo "<td>RM{$item['totalPrice']}</td>";
            echo "<td><button class='btn btn-danger' onclick='removeItem({$item['itemId']})'><i class='fa-regular fa-trash-can'></i> Remove</button></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
    <title>Cart Information</title>
</head>

<body>
<?php include "customernav.php" ?>
    <div class="container mt-4">
        <br>
        <br>
        <h1 class="mb-4">Cart Information</h1>

        <?php
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            displayCartByVendor($_SESSION["cart"]);
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>

        <?php
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])){
        $grandTotal = calculateGrandTotal($_SESSION["cart"]);
        }
        ?>
        <div class="mt-4">
    <?php
    if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])){
        echo "<h5 id='currentAddress'>Delivery Address: "; echo $_SESSION['temporaryAddress'] ?? $customerInfo['personaladdress']; echo"</h5>";
        echo "<h5>Grand Total: RM";echo number_format($grandTotal ?? 0, 2); echo"</h5>";

        echo "<button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#changeAddressModal' style='margin-right: 10px;'><i class='fa-solid fa-location-crosshairs'></i> Change Address</button>";
        echo "<button class='btn btn-success' onclick='checkout()' style='margin-right: 10px;'><i class='fa-regular fa-credit-card'></i> Checkout</button>";



        echo "</div>";
    }
    ?>
</div>

    
<!-- Modal for Changing Address -->
<div class="modal fade" id="changeAddressModal" tabindex="-1" aria-labelledby="changeAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeAddressModalLabel">Change Delivery Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form to input new address -->
                <form id="changeAddressForm">
                    <div class="mb-3">
                        <label for="newAddress" class="form-label">New Address</label>
                        <textarea class="form-control" id="newAddress" name="newAddress" rows="3"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitNewAddress()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


    </div>
    

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function removeItem(itemId) {
            // Use Axios to send the request to remove the item from the cart
            axios.post('removeItem.php', {
                itemId: itemId
            }, {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(function (response) {
                console.log('Server response:', response.data);
                // Reload the page to reflect the updated cart information
                location.reload();
            })
            .catch(function (error) {
                console.error('Error:', error);
            });
        }

        function checkout() {
    // Get the necessary data for the order
    var deliveryAddress = document.getElementById('currentAddress').innerText;
    var quantity = document.getElementById('qtyfield').innerText;
    var grandTotal = <?php echo json_encode($grandTotal ?? 0); ?>; // Assuming you want to pass the grand total to the checkout
    var data = {
        deliveryAddress: deliveryAddress,
        quantity :quantity
    };
    // Use Axios or another method to send the order data to the server for insertion
    axios.post('checkout.php', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
    .then(function (response) {
        alert('Order placed successfully!');
        // Optionally, you can redirect the user to a confirmation page or perform other actions
        location.reload();
    })
    .catch(function (error) {
        console.error('Error placing the order:', error);
    });
}


        function changeAddress() {
            // Implement the logic to change the delivery address here
            alert("Change Address functionality to be implemented.");
        }
        function submitNewAddress() {
    // Get the new address from the form
    var newAddress = document.getElementById('newAddress').value;

    // Store the new address in a session variable
    sessionStorage.setItem('temporaryAddress', newAddress);

    // Update the address displayed on the page (optional)
    document.getElementById('currentAddress').innerText = 'Delivery Address: ' + newAddress;

    // Close the modal
    $('#changeAddressModal').modal('hide');
}

    </script>
</body>

</html>
