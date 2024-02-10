<?php
include "connection.php";
$customerid = $_SESSION['id'];

// Fetch menus for the logged-in vendor
$result = $conn->query("SELECT * FROM customer WHERE id = $customerid");


$row = $result->fetch_assoc();
$customername = $row['Cust_Name'];
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <p class="navbar-brand"><?php echo $customername ?></p>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'customer_mainpage.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="customer_mainpage.php">Menus</a>
                </li>
                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orderhistory.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="orderhistory.php">Orders</a>
                </li>
                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'accountinfo.php' ? 'active' : ''; ?>">
                
                    <a class="nav-link" href="accountinfo.php"><i class="fa-regular fa-user"></i> Account Information</a>
                </li>
                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'customercart.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="customercart.php"><i class="fa-solid fa-cart-shopping"></i> Your Cart</a>
                </li>
                <li class="nav-item">
                    <strong><a class="nav-link" href="logout.php"><i class="fa-solid fa-door-open"></i> Log Out</a></strong>
                </li>
            </ul>
        </div>
    </div>
</nav>
