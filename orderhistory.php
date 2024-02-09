<?php
session_start();
include 'connection.php';

// Check if the customer is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login_customer.php'); // Redirect to login page if not logged in
    exit();
}

$customer_id = $_SESSION['id'];

// Fetch ongoing orders
$ongoingOrders = getOngoing($customer_id, ['Cancelled', 'Completed']);


// Fetch completed orders
$completedOrders = getOrdersByStatus($customer_id, 'Completed');

// Fetch cancelled orders
$cancelledOrders = getOrdersByStatus($customer_id, 'Cancelled');

function getOrdersByStatus($customerId, $status) {
    global $conn;
    $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name, v.Vendor_Name, co.deliveryaddress
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            WHERE co.Customer_ID = $customerId AND co.Order_Status = '$status'";
    
    $result = $conn->query($sql);

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}
function getOngoing($customerId, $excludeStatuses) {
    global $conn;

    // Construct a comma-separated list of excluded statuses
    $excludeStatusesString = implode("', '", $excludeStatuses);

    // Modify the WHERE clause to exclude orders with specific statuses
    $sql = "SELECT co.id AS order_id, m.Item_Name, co.quantity, co.Order_Date, co.Order_Status, ch.Chef_Name, v.Vendor_Name, co.deliveryaddress
            FROM customerorder co
            LEFT JOIN chef ch ON co.Chef_ID = ch.id
            LEFT JOIN menu m ON co.menu_id = m.id
            LEFT JOIN vendor v ON m.vendor_id = v.id
            WHERE co.Customer_ID = $customerId AND co.Order_Status NOT IN ('$excludeStatusesString')";

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
    <title>Customer Orders | Vendor</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
 
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <?php include "customernav.php" ?>
<br>
<br>
<br>
    <div class="container mt-4">
        <!-- Tabs for Ongoing, Completed, and Cancelled Orders -->
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

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Ongoing Orders Tab -->
            <div class="tab-pane fade show active" id="ongoing">
            <?php if (empty($ongoingOrders)) : ?>
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
                    
                        <?php foreach ($ongoingOrders as $order) : ?>
                            
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
                                    <?php if ($order['Order_Status'] === 'Pending') : ?>
                                        <button class="btn btn-danger btn-sm cancel-order-btn" data-order-id="<?php echo $order['order_id']; ?>">Cancel Order</button>
                                    <?php elseif ($order['Order_Status'] === 'Out-For-Delivery' || $order['Order_Status'] === 'In-Transit') : ?>
                                        <button class="btn btn-primary btn-sm view-delivery-info-btn" data-order-id="<?php echo $order['order_id']; ?>">View Delivery Info</button>
                                    <?php endif; ?>
                                    
                                </td>
                            </tr>
                            <!-- Detailed Order Information (Hidden by Default) -->
                            <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                <td colspan="6">
                                    <!-- Display Additional Order Information -->
                                    <div>
                                        <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                                        <strong>Vendor Name:</strong> <?php echo $order['Vendor_Name']; ?><br>
                                        <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Completed Orders Tab -->
            <div class="tab-pane fade" id="completed">
            <?php if (empty($completedOrders)) : ?>
                <!-- Display "No orders found" message -->
                
                <td colspan="6" class="text-center">No ongoing orders found.</td>
                
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
                                <button class="btn btn-primary btn-sm view-delivery-info-btn" data-order-id="<?php echo $order['order_id']; ?>">View Delivery Info</button>
                                </td>
                            </tr>
                            <!-- Detailed Order Information (Hidden by Default) -->
                            <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
                                <td colspan="6">
                                    <!-- Display Additional Order Information -->
                                    <div>
                                        <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                                        <strong>Vendor Name:</strong> <?php echo $order['Vendor_Name']; ?><br>
                                        <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Cancelled Orders Tab -->
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
                                        <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                                        <strong>Vendor Name:</strong> <?php echo $order['Vendor_Name']; ?><br>
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
      

        <!-- Remove this duplicate set -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>



<script>
    $(document).ready(function() {
        // Event listener for switching tabs
        $('#orderTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        document.querySelectorAll('.cancel-order-btn').forEach(function(cancelOrderBtn) {
            cancelOrderBtn.addEventListener('click', function() {
                var orderId = this.dataset.orderId;

                // Implement Axios to cancel the order and update the UI accordingly
                axios.post('cancelorder.php', {
                        order_id: orderId
                    }, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(function(response) {
                        // Update the UI or handle the response as needed
                        if (response.data.success) {
                            // Reload the page or update the UI (e.g., remove the canceled order from the list)
                            console.log(response.data.message);
                            // Reload the page for demonstration purposes; you might want to update the UI without a full page reload
                            location.reload();
                        } else {
                            console.error(response.data.message);
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
            });
        });
        // Event listener for Cancel Order button
        
        $(document).ready(function () {
    // Event listener for View Delivery Info button
    $('.view-delivery-info-btn').click(function (e) {
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
                        .then(function (response) {
                            console.log('Server response:', response.data);

                            // Set the modal body content with the fetched delivery info
                            $('#deliveryInfoModal .modal-body').html(response.data);

                            // Show the modal
                            $('#deliveryInfoModal').modal('show');
                        })
                        .catch(function (error) {
                            console.error('Error:', error);
                        });
                });

    // Toggle details when the button is clicked
    $('.toggle-details-btn').click(function () {
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

    });
</script>



    </div>
</body>

</html>
