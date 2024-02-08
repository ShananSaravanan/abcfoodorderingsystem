<?php
session_start(); // Start the session

include 'connection.php';

// Check if the vendor is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login_vendor.php'); // Redirect to login page if not logged in
    exit();
}

$vendor_id = $_SESSION['id'];
$result = $conn->query("SELECT * FROM menu WHERE vendor_id = $vendor_id");

// Fetch vendor details (optional)
$vendor_result = $conn->query("SELECT * FROM vendor WHERE id = $vendor_id");
$vendor_row = $vendor_result->fetch_assoc();
$vendor_name = $vendor_row['Vendor_Name'];
// Fetch distinct order statuses for dropdown
$statusQuery = $conn->query("SELECT DISTINCT Status FROM customerorder co
                             JOIN menu m ON co.menu_id = m.id
                             WHERE m.vendor_id = $vendor_id");

// Fetch customer orders with necessary joins and vendor_id condition
$orderQuery = $conn->query("
    SELECT co.id AS order_id, co.Order_Date, co.Status,co.deliveryaddress
           c.id AS customer_id, c.Cust_Name, c.Cust_Contact,c.email AS Customer_Email,
           ch.id AS chef_id, ch.Chef_Name, ch.email AS Chef_Email,
           m.Item_Name
    FROM customerorder co
    JOIN customer c ON co.Customer_ID = c.id
    JOIN chef ch ON co.Chef_ID = ch.id
    JOIN menu m ON co.menu_id = m.id
    WHERE m.vendor_id = $vendor_id
");

// Fetch all customer orders
$orders = $orderQuery->fetch_all(MYSQLI_ASSOC);
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
    <!-- Your custom CSS (if any) -->
    <style>
        /* Add your custom styles here */
        body {
            padding-top: 56px;
            /* Adjusted for fixed navbar height */
        }

        @media (min-width: 768px) {
            body {
                padding-top: 0;
            }
        }

        /* Add some margin or padding to create a gap between titles and navbar */
        h2,
        h3 {
            margin-top: 20px;
        }

        /* ... (existing styles) ... */

        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            /* Change left to right */
            z-index: 1000;
        }

        .tooltip-container {
            position: relative;
            display: inline-block;
        }

        .tooltip-text {
            visibility: hidden;
            width: auto;
            white-space: nowrap;
            /* Prevent line breaks */
            background-color: #007bff;
            /* Adjust the color as needed */
            color: #fff;
            /* Text color */
            text-align: center;
            padding: 5px;
            border-radius: 5px;
            position: absolute;
            z-index: 1;
            top: 50%;
            right: 120%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
            transform: translateY(-50%) translateX(-10px);
            /* Adjust the distance to the left */
        }
    </style>
</head>

<body>

<?php include 'vendornav.php'; ?>

    <div class="container-fluid mt-4">
        <br>
        <br>
        <div class="form-group">
            <h2 class="titles">Customer Orders</h2>
            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Menu Name</th>
                            <th>Customer Name</th>
                            <th>Order Date Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody">
                    <?php foreach ($orders as $order) : ?>
    <tr class="info-row">
        <td><?php echo $order['order_id']; ?></td>
        <td><?php echo $order['Item_Name']; ?></td>
        <td><?php echo $order['Cust_Name']; ?></td>
        <td><?php echo date('d-m-Y H:i:s', strtotime($order['Order_Date'])); ?></td>
        <td class="<?php echo getStatusClass($order['Status']); ?>"><?php echo $order['Status']; ?></td>
        <td>
            <button class="btn btn-light btn-sm show-details-btn" data-order-id="<?php echo $order['order_id']; ?>">
                <i class="fa-solid fa-toggle-off"></i>
                <span class="button-text">Toggle Details</span>
            </button>
        </td>
    </tr>
    <tr class="details-row" id="detailsRow<?php echo $order['order_id']; ?>" style="display: none;">
        <td colspan="6">
            <div>
                <strong>Customer ID:</strong> <?php echo $order['customer_id']; ?><br>
                <strong>Customer Contact:</strong> <?php echo $order['Cust_Contact']; ?><br>
                <strong>Customer Email:</strong> <?php echo $order['Customer_Email']; ?><br>
                <strong>Chef ID:</strong> <?php echo $order['chef_id']; ?><br>
                <strong>Chef Name:</strong> <?php echo $order['Chef_Name']; ?><br>
                <strong>Chef Email:</strong> <?php echo $order['Chef_Email']; ?><br>
                <strong>Delivery Address:</strong> <?php echo $order['deliveryaddress']; ?><br>
            </div>
        </td>
    </tr>
<?php endforeach; ?>

<?php
function getStatusClass($status)
{
    switch ($status) {
        case 'Completed':
            return 'text-success'; // Green color for active
        case 'Cancelled':
            return 'text-danger'; // Red color for cancelled
        case 'Ongoing':
                return 'text-primary'; // Red color for cancelled
        
        // Add more cases as needed
        default:
            return '';
    }
}
?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    var infoRows = document.getElementsByClassName('info-row');
    var detailsRows = document.getElementsByClassName('details-row');
    var showDetailsBtns = document.getElementsByClassName('show-details-btn');

    document.getElementById('searchInput').addEventListener('input', function () {
        var searchValue = this.value.toLowerCase();

        for (var i = 0; i < infoRows.length; i++) {
            var cells = infoRows[i].getElementsByTagName('td');
            var found = false;

            for (var j = 0; j < cells.length; j++) {
                var cellText = cells[j].innerText.toLowerCase();

                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            if (found) {
                infoRows[i].style.display = '';
                detailsRows[i].style.display = (detailsRows[i].style.display === 'none') ? 'none' : '';
                showDetailsBtns[i].style.display = ''; // Show the button
            } else {
                infoRows[i].style.display = 'none';
                detailsRows[i].style.display = 'none';
                showDetailsBtns[i].style.display = 'none'; // Hide the button
            }
        }
    });

    // Toggle details when the button is clicked
    for (var i = 0; i < showDetailsBtns.length; i++) {
        showDetailsBtns[i].addEventListener('click', function () {
            var orderId = this.getAttribute('data-order-id');
            var detailsRow = document.getElementById('detailsRow' + orderId);

            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = '';
                this.innerHTML = '<i class="fa-solid fa-toggle-on"></i> Toggle Details';
            } else {
                detailsRow.style.display = 'none';
                this.innerHTML = '<i class="fa-solid fa-toggle-off"></i> Toggle Details';
            }
        });
    }
</script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
