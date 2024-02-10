<?php
session_start();
include 'connection.php';

// Check if the chef is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login_chef.php'); // Redirect to login page if not logged in or not a chef
    exit();
}
$_SESSION['nav'] = "chefnav.php";
$chef_id = $_SESSION['id'];

// Fetch ongoing orders for the chef
$ongoingOrders = getChefOrders($chef_id, ['Cancelled', 'Completed']);
$completedOrders = getOrdersByStatus($chef_id,'Completed');
$cancelledOrders = getOrdersByStatus($chef_id,'Cancelled');
function getOrdersByStatus($chef_id, $status) {
    global $conn;
    $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name, v.Vendor_Name, co.deliveryaddress,cu.id AS customer_id, 
            cu.Cust_Name, cu.Cust_Contact
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN customer cu ON co.Customer_ID = cu.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            WHERE co.Chef_ID = $chef_id AND co.Order_Status = '$status'";
    
    $result = $conn->query($sql);

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}
function getChefOrders($chefId, $excludeStatuses)
{
    global $conn;

    // Construct a comma-separated list of excluded statuses
    $excludeStatusesString = implode("', '", $excludeStatuses);

    // Modify the WHERE clause to exclude orders with specific statuses
    $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name, v.Vendor_Name, co.deliveryaddress, cu.id AS customer_id, 
            cu.Cust_Name, cu.Cust_Contact
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN customer cu ON co.Customer_ID = cu.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            WHERE ch.id = $chefId AND co.Order_Status NOT IN ('$excludeStatusesString')";

    $result = $conn->query($sql);

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chef Orders | Vendor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery (you can use a newer version if available) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            padding: 12px 16px;
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-item {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include "chefnav.php" ?>;
    <br>
    <br>
    <br>
    <div class="container mt-4">
    <ul class="nav nav-tabs" id="orderTabs">
            <li class="nav-item">
                <a class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" href="#ongoing">Ongoing Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed">Completed Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cancelled-tab" data-bs-toggle="tab" href="#cancelled">Cancelled Orders</a>
            </li>
        </ul>
        <div class="tab-content">
            <!-- Ongoing Orders Tab -->
            <div class="tab-pane fade show active" id="ongoing">
                <?php if (empty($ongoingOrders)) : ?>
                    <!-- Display "No orders found" message -->
                    <p colspan="6">No ongoing orders found.</p>
                <?php else : ?>
                    <table class="table">
                        <!-- Table Headers -->
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Menu Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody id="ongoingOrdersBody">
                            <?php foreach ($ongoingOrders as $order) : ?>
                                <!-- Display Simplified Order Information -->
                                <tr class="info-row">
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['Item_Name']; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo date('d-m-Y H:i:s', strtotime($order['Order_Date'])); ?></td>
                                    <td><?php echo $order['Order_Status']; ?></td>
                                    <td>
                                    
                                        
                                        <!-- Set Status Buttons -->
                                        <button class="btn btn-dark btn-sm toggle-details-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                            <i class="fa-solid fa-toggle-off"></i>
                                            <span class="button-text">Toggle Details</span>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-info dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Set Status
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="statusDropdown">
                                                <a class="dropdown-item" href="#" onclick="showConfirmationModal(<?php echo $order['order_id']; ?>, 'In-Kitchen')">In-Kitchen</a>
                                                <a class="dropdown-item" href="#" onclick="showConfirmationModal(<?php echo $order['order_id']; ?>, 'Waiting For Pickup')">Waiting For Pickup</a>
                                                <a class="dropdown-item" href="#" onclick="showConfirmationModal(<?php echo $order['order_id']; ?>, 'Cancelled')">Cancelled</a>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <button class="btn btn-primary btn-sm view-delivery-info-btn" data-order-id="<?php echo $order['order_id']; ?>" <?php if ($order['Order_Status'] != 'Waiting For Pickup') : ?> style="display: none;" <?php endif; ?>>View Delivery Info</button>
                                        
                                    </td>
                                </tr>
                                <!-- Detailed Order Information (Hidden by Default) -->
                                <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                    <td colspan="6">
                                        <!-- Display Additional Order Information -->
                                        <div>
                                            <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                                            <strong>Customer Name:</strong> <?php echo $order['Cust_Name']; ?><br>
                                            <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
                                            <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="completed">
                <?php if (empty($completedOrders)) : ?>
                    <!-- Display "No orders found" message -->
                    <p colspan="6">No completed orders found.</p>
                <?php else : ?>
                    <table class="table">
                        <!-- Table Headers -->
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Menu Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody id="ongoingOrdersBody">
                            <?php foreach ($completedOrders as $order) : ?>
                                <!-- Display Simplified Order Information -->
                                <tr class="info-row">
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['Item_Name']; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo date('d-m-Y H:i:s', strtotime($order['Order_Date'])); ?></td>
                                    <td><?php echo $order['Order_Status']; ?></td>
                                    <td>
                                        <button class="btn btn-light btn-sm toggle-details-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                            <i class="fa-solid fa-toggle-off"></i>
                                            <span class="button-text">Toggle Details</span>
                                        </button>
                                        <!-- Set Status Buttons -->
                                        <button class="btn btn-primary btn-sm view-delivery-info-btn" data-order-id="<?php echo $order['order_id']; ?>">View Delivery Info</button>
                                        

                                    </td>
                                </tr>
                                <!-- Detailed Order Information (Hidden by Default) -->
                                <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                    <td colspan="6">
                                        <!-- Display Additional Order Information -->
                                        <div>
                                            <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                                            <strong>Customer Name:</strong> <?php echo $order['Cust_Name']; ?><br>
                                            <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
                                            <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="cancelled">
            <?php if (empty($cancelledOrders)) : ?>
                <!-- Display "No orders found" message -->
                
                    <p colspan="6">No ongoing orders found.</p>
                
            <?php else : ?>
            <table class="table">
                    <!-- Table Headers -->
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Menu Name</th>
                            <th>Quantity</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <!-- Table Body -->
                    <tbody id="cancelledOrdersBody">
                        <?php foreach ($cancelledOrders as $order) : ?>
                            <!-- Display Simplified Order Information -->
                            <tr class="info-row">
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['Item_Name']; ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo date('d-m-Y H:i:s', strtotime($order['Order_Date'])); ?></td>
                                <td><?php echo $order['Order_Status']; ?></td>
                                <td>
                                <button class="btn btn-light btn-sm toggle-details-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                <i class="fa-solid fa-toggle-off"></i>
                                <span class="button-text">Toggle Details</span>
                                </button>
                                </td>
                            </tr>
                            <!-- Detailed Order Information (Hidden by Default) -->
                            <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                <td colspan="6">
                                    <!-- Display Additional Order Information -->
                                    <div>
                                            <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                                            <strong>Customer Name:</strong> <?php echo $order['Cust_Name']; ?><br>
                                            <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
                                            <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
                                        </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="modal fade" id="deliveryInfoModal" tabindex="-1" role="dialog" aria-labelledby="deliveryInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="deliveryInfoModalLabel">Delivery Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                    </div>
                    <div class="modal-body">
                        <!-- Delivery info will be loaded here using AJAX -->
                    </div>
                </div>
            </div>
        </div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmationModalBody">
                <!-- Dynamic content goes here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirm</button>
            </div>
        </div>
    </div>
</div>



        <script>
    var selectedOrderId;
    var selectedStatus;

    function showConfirmationModal(orderId, status) {
        selectedOrderId = orderId;
        selectedStatus = status;

        // Update the modal content with the selected status
        $('#confirmationModalBody').html('Are you sure you want to change the order status to: ' + status);

        // Show the confirmation modal
        $('#confirmationModal').modal('show');
    }

    // Event listener for confirm button in the confirmation modal
    $('#confirmStatusChange').click(function () {
    // Close the confirmation modal
    $('#confirmationModal').modal('hide');

    // Send AJAX request to update the order status
    updateOrderStatus(selectedOrderId, selectedStatus);
});

function updateOrderStatus(orderId, newStatus) {
    // Implement AJAX to send a request to update_order_status.php
    axios.post('orderstatus.php', {
        orderId: orderId,
        newStatus: newStatus
    }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(function (response) {
        console.log('Server response:', response.data);
        if (newStatus === 'Waiting For Pickup') {
            checkOrderInDeliveryHistory(orderId);
        }
        location.reload();
    })
    .catch(function (error) {
        console.error('Error:', error);

        // Handle errors, if any
    });
}
function checkOrderInDeliveryHistory(orderId) {
    // Implement AJAX to check if the order exists in deliveryhistory.php
    axios.post('check_delivery_history.php', {
        orderId: orderId
    }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(function (response) {
        console.log('Server response:', response.data);

        // If the order exists in deliveryhistory, show the "View Delivery Info" button
        if (response.data.exists) {
            $('.view-delivery-info-btn[data-order-id="' + orderId + '"]').show();
            location.reload();
        }

        // Reload the page
        
    })
    .catch(function (error) {
        console.error('Error:', error);

        // Handle errors, if any
    });
}

            $(document).ready(function() {
                // Event listener for switching tabs
                $('#orderTabs a').on('click', function(e) {
                    e.preventDefault();
                    $(this).tab('show');
                });

                $('.view-delivery-info-btn').click(function(e) {
                    e.preventDefault();

                    var orderId = $(this).data('order-id');

                    // Implement AJAX using Axios to fetch delivery info and display it in the modal
                    axios.post('fetch_delivery_info.php', {
                            order_id: orderId
                        }, {
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        })
                        .then(function(response) {
                            console.log('Server response:', response.data);

                            // Set the modal body content with the fetched delivery info
                            $('#deliveryInfoModal .modal-body').html(response.data);

                            // Show the modal
                            $('#deliveryInfoModal').modal('show');
                            
                        })
                        .catch(function(error) {
                            console.error('Error:', error);
                        });
                });

                // Toggle details when the button is clicked
                $('.toggle-details-btn').click(function() {
                    var orderId = $(this).data('order-id');
                    var detailsRow = $('#detailsRow' + orderId);

                    if (detailsRow.is(':visible')) {
                        detailsRow.hide();
                        $(this).html('<i class="fa-solid fa-toggle-off"></i> Toggle Details');
                    } else {
                        detailsRow.show();
                        $(this).html('<i class="fa-solid fa-toggle-on"></i> Toggle Details');
                    }
                });
            });


            function toggleDropdown() {
                var dropdownContent = document.getElementById("dropdownContent");
                dropdownContent.style.display = (dropdownContent.style.display === "block") ? "none" : "block";
            }
        </script>
    </div>
</body>

</html>