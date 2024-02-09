<?php
session_start();

include 'connection.php';

if (!isset($_SESSION['id'])) {
    header('Location: login_vendor.php');
    exit();
}

$vendor_id = $_SESSION['id'];
$result = $conn->query("SELECT * FROM menu WHERE vendor_id = $vendor_id");

$vendor_result = $conn->query("SELECT * FROM vendor WHERE id = $vendor_id");
$vendor_row = $vendor_result->fetch_assoc();
$vendor_name = $vendor_row['Vendor_Name'];

$deliveryInfoQuery = $conn->query("
    SELECT dh.id AS deliveryhistory_id, c.Cust_Name,c.email AS Customer_Email,c.Cust_contact,co.Customer_ID, m.Item_Name,
           dp.Personnel_Name,dp.email AS Personnel_Email,dh.Personnel_ID, dp.Contact_Information, dh.Delivery_Date, dh.Delivery_Status
           
    FROM deliveryhistory dh
    JOIN customerorder co ON dh.Order_ID = co.id
    JOIN customer c ON co.Customer_ID = c.id
    JOIN menu m ON co.menu_id = m.id
    JOIN deliverypersonnel dp ON dh.Personnel_ID = dp.id
    WHERE m.vendor_id = $vendor_id
");

$deliveryInfo = $deliveryInfoQuery->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Information | Vendor</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
    <!-- Your custom CSS (if any) -->
    <style>
        body {
            padding-top: 56px;
        }

        @media (min-width: 768px) {
            body {
                padding-top: 0;
            }
        }

        h2,
        h3 {
            margin-top: 20px;
        }

        .container-fluid {
            margin-top: 20px;
        }

        /* Add your custom styles here */
        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
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
            background-color: #007bff;
            color: #fff;
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
        }
    </style>
</head>

<body>

    <?php include 'vendornav.php'; ?>

    <div class="container-fluid">
        <br>
        <br>
        <h2 class="titles">Delivery Information</h2>

        <input type="text" class="form-control mb-3" id="searchInput" placeholder="Search...">

        <table class="table">
            <thead>
                <tr>
                    <th>Delivery History ID</th>
                    <th>Customer Name</th>
                    <th>Menu Name</th>
                    <th>Delivery Personnel Name</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th>Action</th> <!-- Added column for the toggle button -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deliveryInfo as $delivery) : ?>
                    <tr class="info-row">
                        <td><?php echo $delivery['deliveryhistory_id']; ?></td>
                        <td><?php echo $delivery['Cust_Name']; ?></td>
                        <td><?php echo $delivery['Item_Name']; ?></td>
                        <td><?php echo $delivery['Personnel_Name']; ?></td>
                        <td><?php echo date('d-m-Y H:i:s', strtotime($delivery['Delivery_Date'])); ?></td>
                        <td class="<?php echo getStatusClass($delivery['Delivery_Status']); ?>"><?php echo $delivery['Delivery_Status']; ?></td>
                        <td>
                            <button class="btn btn-light btn-sm show-details-btn" data-delivery-id="<?php echo $delivery['deliveryhistory_id']; ?>">
                                <i class="fa-solid fa-toggle-off"></i>
                                <span class="button-text">Toggle Details</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="details-row" id="detailsRow<?php echo $delivery['deliveryhistory_id']; ?>" style="display: none;">
                        <td colspan="7">
                            <div>
                                <!-- Add more details as needed -->
                                <strong>Customer ID:</strong> <?php echo $delivery['Customer_ID']; ?><br>
                                <strong>Customer Contact:</strong> <?php echo $delivery['Cust_contact']; ?><br>
                                <strong>Customer Email:</strong> <?php echo $delivery['Customer_Email']; ?><br>
                                <strong>Personnel ID:</strong> <?php echo $delivery['Personnel_ID']; ?><br>
                                <strong>Personnel Contact:</strong> <?php echo $delivery['Contact_Information']; ?><br>
                                <strong>Personnel Email:</strong> <?php echo $delivery['Personnel_Email']; ?><br>
                                <!-- Add other details here -->
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php
function getStatusClass($status)
{
    switch ($status) {
        case 'Delivered':
            return 'text-success'; // Green color for active
        case 'Returned':
            return 'text-danger'; // Red color for cancelled
        case 'In-Transit':
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
                var deliveryId = this.getAttribute('data-delivery-id');
                var detailsRow = document.getElementById('detailsRow' + deliveryId);

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
