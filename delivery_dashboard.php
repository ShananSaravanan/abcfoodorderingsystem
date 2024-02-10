<?php
session_start();
include 'connection.php';

// Check if the chef is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login_delivery.php'); // Redirect to login page if not logged in or not a chef
    exit();
}

$delivery_id = $_SESSION['id'];
$_SESSION['nav'] = "deliverynav.php";

// Fetch ongoing orders for the chef
$pendingOrders = getPendingOrders();
$acceptedOrders = getAcceptedOrders($delivery_id,['In-Transit','Out-For Delivery']);
$completedOrders = getOrdersByStatus($delivery_id,'Delivered');
$cancelledOrders = getOrdersByStatus($delivery_id,'Cancelled');
function getOrdersByStatus($delivery_id, $status) {
    global $conn;
    $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name,v.Vendor_Address,v.Vendor_Contact, v.Vendor_Name, co.deliveryaddress,cu.id AS customer_id, 
            cu.Cust_Name, cu.Cust_Contact,ch.id AS Chef_ID, Chef_Name,dh.Delivery_Status
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN deliveryhistory dh ON co.id = dh.Order_ID
            LEFT JOIN customer cu ON co.Customer_ID = cu.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            LEFT JOIN deliverypersonnel dp ON dh.Personnel_ID = dp.id
            WHERE dp.id = $delivery_id AND dh.Delivery_Status = '$status'";
    
    $result = $conn->query($sql);

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}
function getAcceptedOrders($delivery_id, $statuses) {
    global $conn;

    // Make sure to properly escape and sanitize the input values to prevent SQL injection
    $delivery_id = mysqli_real_escape_string($conn, $delivery_id);

    // Use IN clause to check for multiple statuses
    $statusString = "'" . implode("','", $statuses) . "'";
    
    $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name, v.Vendor_Address, v.Vendor_Contact, v.Vendor_Name, co.deliveryaddress, cu.id AS customer_id, 
            cu.Cust_Name, cu.Cust_Contact, ch.id AS Chef_ID, Chef_Name, dh.Delivery_Status
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN deliveryhistory dh ON co.id = dh.Order_ID
            LEFT JOIN customer cu ON co.Customer_ID = cu.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            LEFT JOIN deliverypersonnel dp ON dh.Personnel_ID = dp.id
            WHERE dp.id = $delivery_id AND dh.Delivery_Status IN ($statusString)";
    
    $result = $conn->query($sql);

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}
function getPendingOrders() {
    global $conn;
   $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name,v.Vendor_Address,v.Vendor_Contact, v.Vendor_Name, co.deliveryaddress, cu.id AS customer_id, 
            cu.Cust_Name, cu.Cust_Contact, ch.id AS Chef_ID, Chef_Name
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN deliveryhistory dh ON co.id = dh.Order_ID
            LEFT JOIN customer cu ON co.Customer_ID = cu.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            LEFT JOIN deliverypersonnel dp ON dh.Personnel_ID = dp.id
            WHERE (dh.Order_ID IS NULL OR dh.Delivery_Status = 'cancelled')";
            
    
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
    <?php include "deliverynav.php" ?>;
    <br>
    <br>
    <br>
    <div class="container mt-4">
    <ul class="nav nav-tabs" id="orderTabs">
            <li class="nav-item">
                <a class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" href="#pending">Pending Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#accepted">Accepted Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cancelled-tab" data-bs-toggle="tab" href="#completed">Compeleted Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cancelled-tab" data-bs-toggle="tab" href="#cancelled">Cancelled Orders</a>
            </li>
        </ul>
        <div class="tab-content">
            <!-- Ongoing Orders Tab -->
            <div class="tab-pane fade show active" id="pending">
                <?php if (empty($pendingOrders)) : ?>
                    <!-- Display "No orders found" message -->
                    <p colspan="6">No ongoing orders found.</p>
                <?php else : ?>
                    <table class="table" id="pendingOrdersTable">
                        <!-- Table Headers -->
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Menu Name</th>
                                <th>Quantity</th>
                                <th>Vendor Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody id="pendingOrdersBody">
                            <?php foreach ($pendingOrders as $order) : ?>
                                <!-- Display Simplified Order Information -->
                                <tr class="info-row">
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['Item_Name']; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo $order['Vendor_Name']; ?></td>
                                    <td><?php echo $order['Order_Status']; ?></td>
                                    <td>
                                    
                                        
                                        <!-- Set Status Buttons -->
                                        <button class="btn btn-dark btn-sm toggle-details-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                            <i class="fa-solid fa-toggle-off"></i>
                                            <span class="button-text">Toggle Details</span>
                                        </button>
                                        <button class="btn btn-success btn-sm accept-order-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                        <i class="fa-regular fa-circle-check"></i>
                                            <span class="button-text">Accept</span>
                                        </button>
                                        
                                        
                                        
                                        
                                        
                                    </td>
                                </tr>
                                <!-- Detailed Order Information (Hidden by Default) -->
                                <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                    <td colspan="6">
                                        <!-- Display Additional Order Information -->
                                        <div>
                                            <strong>Vendor Address:</strong> <?php echo $order['Vendor_Address']; ?><br>
                                            <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="accepted">
                <?php if (empty($acceptedOrders)) : ?>
                    <!-- Display "No orders found" message -->
                    <p colspan="6">No accepted orders found.</p>
                <?php else : ?>
                    <table class="table" id="acceptedOrdersTable">
                        <!-- Table Headers -->
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Menu Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Vendor Address</th>
                                <th>Delivery Address</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody id="acceptedOrdersBody">
                            <?php foreach ($acceptedOrders as $order) : ?>
                                <!-- Display Simplified Order Information -->
                                <tr class="info-row">
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['Item_Name']; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo date('d-m-Y H:i:s', strtotime($order['Order_Date'])); ?></td>
                                    <td><?php echo $order['Vendor_Address']; ?></td>
                                    <td><?php echo $order['deliveryaddress']; ?></td>
                                    <td><?php echo $order['Delivery_Status']; ?></td>
                                    <td>
                                        
                                       
                                        <div class="dropdown">
                                            <button class="btn btn-info dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Set Status
                                            </button>
                                            
                                            <div class="dropdown-menu" aria-labelledby="statusDropdown">
                                                <a class="dropdown-item" href="#" onclick="showConfirmationModal(<?php echo $order['order_id']; ?>, 'Out-For Delivery')">Out-For Delivery</a>
                                                <a class="dropdown-item" href="#" onclick="showConfirmationModal(<?php echo $order['order_id']; ?>, 'Delivered')">Delivered</a>
                                                <a class="dropdown-item" href="#" onclick="showConfirmationModal(<?php echo $order['order_id']; ?>, 'Cancelled')">Cancelled</a>
                                            </div>
                                        </div>
                                        <br>
                                        <br>
                                        
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
                                            <strong>Vendor Name:</strong> <?php echo $order['Vendor_Name']; ?><br>
                                            <strong>Vendor Contact:</strong> <?php echo $order['Vendor_Contact']; ?><br>
                                            <strong>Chef ID:</strong> <?php echo $order['Chef_ID']; ?><br>
                                            <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                                            <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                                            <strong>Customer Name:</strong> <?php echo $order['Cust_Name']; ?><br>
                                            <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
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
                    <table class="table" id="completedOrdersTable">
                        <!-- Table Headers -->
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Menu Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Vendor Address</th>
                                <th>Delivery Address</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody id="completedOrdersBody">
                            <?php foreach ($completedOrders as $order) : ?>
                                <!-- Display Simplified Order Information -->
                                <tr class="info-row">
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['Item_Name']; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo date('d-m-Y H:i:s', strtotime($order['Order_Date'])); ?></td>
                                    <td><?php echo $order['Vendor_Address']; ?></td>
                                    <td><?php echo $order['deliveryaddress']; ?></td>
                                    <td><?php echo $order['Delivery_Status']; ?></td>
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
                                            <strong>Vendor Name:</strong> <?php echo $order['Vendor_Name']; ?><br>
                                            <strong>Vendor Contact:</strong> <?php echo $order['Vendor_Contact']; ?><br>
                                            <strong>Chef ID:</strong> <?php echo $order['Chef_ID']; ?><br>
                                            <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                                            <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                                            <strong>Customer Name:</strong> <?php echo $order['Cust_Name']; ?><br>
                                            <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
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
                    <p colspan="6">No cancelled orders found.</p>
                <?php else : ?>
                    <table class="table" id="acancelledOrdersTable">
                        <!-- Table Headers -->
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Menu Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Vendor Address</th>
                                <th>Delivery Address</th>
                                <th>Status</th>
                                
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
                                    <td><?php echo $order['Vendor_Address']; ?></td>
                                    <td><?php echo $order['deliveryaddress']; ?></td>
                                    <td><?php echo $order['Delivery_Status']; ?></td>
                                    <!-- <td>
                                        <button class="btn btn-light btn-sm toggle-details-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                            <i class="fa-solid fa-toggle-off"></i>
                                            <span class="button-text">Toggle Details</span>
                                        </button>
                                        
                                    </td> -->
                                </tr>
                                <!-- Detailed Order Information (Hidden by Default) -->
                                <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                    <td colspan="6">
                                        <!-- Display Additional Order Information -->
                                        <div>
                                            <strong>Vendor Name:</strong> <?php echo $order['Vendor_Name']; ?><br>
                                            <strong>Vendor Contact:</strong> <?php echo $order['Vendor_Contact']; ?><br>
                                            <strong>Chef ID:</strong> <?php echo $order['Chef_ID']; ?><br>
                                            <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                                            <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                                            <strong>Customer Name:</strong> <?php echo $order['Cust_Name']; ?><br>
                                            <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
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
    console.log(orderId,newStatus);
    axios.post('deliverystatus.php', {
        orderId: orderId,
        newStatus: newStatus
    }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(function (response) {
        console.log('Server response:', response.data);
        
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

                $('.accept-order-btn').click(function() {
                    var orderId = $(this).data('order-id');

                    axios.post('accept_order.php', {
                        orderId: orderId
                        }, {
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        })
                        .then(function(response) {
                            console.log('Server response:', response.data);
                            alert("ORDER ID: "+orderId+"  has been accepted!");
                            location.reload();
                        })
                        .catch(function(error) {
                            console.error('Error:', error);
                        });
                    
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