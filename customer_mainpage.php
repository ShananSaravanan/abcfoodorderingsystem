<?php
session_start();
include 'connection.php';
if (!isset($_SESSION['id'])) {
    header('Location: login_customer.php'); // Redirect to login page if not logged in
    exit();
}

$customer_id = $_SESSION['id'];

$sql = "SELECT m.id, m.Item_Name, m.Item_Description, m.Price, m.menu_img, v.Vendor_Name, v.Vendor_Address
        FROM menu m
        JOIN vendor v ON m.vendor_id = v.id";
$result = $conn->query($sql);

$menus = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Extract city and state from the address
        $addressParts = explode(', ', $row['Vendor_Address']);
        $state = end($addressParts);
        $city = prev($addressParts); // Get the element before the last one

        $cityState = $city . ', ' . $state;

        $menus[$row['Vendor_Name']][] = [
            'id' => $row['id'],
            'Item_Name' => $row['Item_Name'],
            'Item_Description' => $row['Item_Description'],
            'Price' => $row['Price'],
            'menu_img' => $row['menu_img'],
            'Vendor_Name' => $row['Vendor_Name'],
            'Vendor_Address' => $cityState,
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>Menu Ordering</title>
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
    <style>
        .vendor-container {
            margin-bottom: 20px;
        }

        .vendor-name {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            color: #fff; /* Set default text color to white */
        }

        <?php
        // Generate unique background colors based on the vendor names
        $colorMap = [];
        foreach ($menus as $vendorName => $menuItems) {
            if (!isset($colorMap[$vendorName])) {
                $colorMap[$vendorName] = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Generate a random hex color
            }
        }

        // Apply different background colors to each vendor name and set text color dynamically
        foreach ($colorMap as $vendorName => $backgroundColor) {
            // Calculate brightness of the background color
            $brightness = (hexdec(substr($backgroundColor, 1, 2)) * 299 +
                hexdec(substr($backgroundColor, 3, 2)) * 587 +
                hexdec(substr($backgroundColor, 5, 2)) * 114) / 1000;

            // Determine text color based on brightness
            $textColor = $brightness > 128 ? '#000' : '#fff';

            echo ".vendor-name[data-vendor='{$vendorName}'] { background-color: {$backgroundColor}; color: {$textColor}; }\n";
        }
        ?>

        .menu-card {
            display: block;
            position: relative;
            margin-bottom: 20px;
            text-align: center;
            overflow: hidden;
        }

        .menu-card img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-bottom: 1px solid #dee2e6;
        }

        .menu-details {
            padding: 15px;
            text-align: left;
            border: 1px solid #dee2e6;
            border-top: none;
        }

        .menu-details h5 {
            margin-bottom: 0;
        }

        .add-to-cart {
            text-align: center;
            padding: 15px;
            border: 1px solid #dee2e6;
        }

        @media (min-width: 768px) {
            .menu-card {
                flex: 0 0 calc(33.333% - 20px);
            }
        }
    </style>
</head>

<body>
    <?php include "customernav.php" ?>

    <div class="container-fluid mt-4">
        <br>
        <br>
        <h1 class="mb-4">Menu Ordering</h1>
        <div class="mb-4">
            <input type="text" id="searchInput" class="form-control mb-4" placeholder="Search menu items">
        </div>

        <?php
        foreach ($menus as $vendorName => $menuItems) :
        ?>
            <div class="vendor-container">
                <div class="vendor-name" data-vendor="<?= $vendorName ?>">
                    <h4><?= $vendorName ?></h4>
                </div>
                <div class="row">
                    <?php foreach ($menuItems as $menu) : ?>
                        <div class="col-md-3 mb-4 menu-card">
                            <div class="menu-card d-flex flex-column h-100">
                                <img src="<?= $menu['menu_img'] ?>" class="card-img-top custom-menu-image" alt="<?= $menu['Item_Name'] ?>">
                                <div class="menu-details flex-grow-1">
                                    <h5 class="card-title"><?= $menu['Item_Name'] ?></h5>
                                    <p class="card-text"><?= $menu['Item_Description'] ?></p>
                                    <p class="card-text"><strong>Price:</strong> RM<?= $menu['Price'] ?></p>
                                    <p class="card-text"><strong>Delivered From:</strong> <?= $cityState ?></p>
                                </div>
                                <div class="add-to-cart">
                                <button class="btn btn-primary w-100" onclick="showQuantityModal(<?= $menu['id'] ?>, '<?= $menu['Vendor_Name'] ?>','<?= $cityState ?>', '<?= $menu['Item_Name'] ?>', <?= $menu['Price'] ?>)" data-item-id="<?= $menu['id'] ?>">Add to Cart</button>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quantity Selection Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quantityModalLabel">Select Quantity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Vendor:</strong> <span id="vendorName"></span></p>
                <p><strong>Menu:</strong> <span id="menuName"></span></p>
                <p><strong>Price:</strong> RM<span id="itemPrice"></span></p>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" class="form-control" min="1" value="1">
                <p><strong>Total Price:</strong> RM<span id="totalPrice">0.00</span></p> <!-- New line to display total price -->
                <input type="hidden" id="deliveryaddress" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addToCart()">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS and Popper.js -->
    <!-- jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            var searchTerm = this.value.toLowerCase();

            // Loop through all menu cards
            document.querySelectorAll('.menu-card').forEach(function (card) {
                var itemName = card.querySelector('.menu-details h5').innerText.toLowerCase();

                // Show or hide the card based on whether the search term is present in the item name
                if (itemName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        function showQuantityModal(itemId,vendorName,shortAddress,  menuName, itemPrice) {
    // Update modal content with item details
    document.getElementById('vendorName').innerText = vendorName;
    document.getElementById('menuName').innerText = menuName;
    document.getElementById('itemPrice').innerText = itemPrice;
    document.getElementById('totalPrice').innerText = itemPrice;
    document.getElementById('deliveryaddress').value = shortAddress;      
    // Reset quantity to 1
    document.getElementById('quantity').value = 1;

    // Set the itemId in the modal for later use
    document.getElementById('quantityModal').dataset.itemId = itemId;

    // Show the modal
    var modal = new bootstrap.Modal(document.getElementById('quantityModal'));
    modal.show();
}


function addToCart() {
    var quantity = document.getElementById('quantity').value;
    var itemName = document.getElementById('menuName').innerText;
    var itemPrice = parseFloat(document.getElementById('itemPrice').innerText);
    var vendorName = document.getElementById('vendorName').innerText;
    var itemId = parseInt(document.getElementById('quantityModal').dataset.itemId);
    var deliveryinfo =document.getElementById('deliveryaddress').value;
    var totalPrice =  document.getElementById('totalPrice').innerText;
    // Additional details to be sent to addToCart.php
    var data = {
        itemId: itemId,
        quantity: parseInt(quantity),
        vendorName: vendorName,
        itemName: itemName,
        price :itemPrice,
        totalprice :totalPrice,
        deliveryInfo: deliveryinfo // Replace with actual delivery information
    };

    if (!isNaN(quantity) && quantity > 0) {
        // Use Axios to send the data to the server and update the session/cart
        axios.post('addToCart.php', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(function (response) {
            console.log('Server response:', response.data);
            // Handle the response here
            if (response.data.success) {
                alert(quantity + " " + itemName + "(s) added to the cart. Total Price: RM" + (quantity * itemPrice));
                // Close the modal
                var modal = new bootstrap.Modal(document.getElementById('quantityModal'));
                modal.hide();
            } else {
                alert("Failed to add item to cart. " + response.data.message);
            }
        })
        .catch(function (error) {
            console.error('Error:', error);
        });
    } else {
        alert("Invalid quantity. Please enter a valid number greater than 0.");
    }
}







        document.getElementById('quantity').addEventListener('input', function () {
        
            updateTotalPrice();
    });

    function updateTotalPrice() {
        
        var quantity = document.getElementById('quantity').value;
        var itemPrice = parseFloat(document.getElementById('itemPrice').innerText);

        if (!isNaN(quantity) && quantity > 0) {
            
            var totalPrice = (quantity * itemPrice).toFixed(2);
            document.getElementById('totalPrice').innerText = totalPrice;
        } else {
            document.getElementById('totalPrice').innerText = 'Invalid quantity';
        }
    }
    </script>
</body>

</html>
