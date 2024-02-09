<?php
include "connection.php";
$chefid = $_SESSION['id'];

// Fetch menus for the logged-in vendor
$result = $conn->query("SELECT * FROM chef WHERE id = $chefid");


$row = $result->fetch_assoc();
$chefname = $row['Chef_Name'];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <p class="navbar-brand"><?php echo $chefname; ?>'s Chef Dashboard</p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'chef_dashboard.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="chef_dashboard.php.php">Orders</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="account.php">Account Information</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'ims.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="deliveryhistory.php">Inventory Management System</a>
            </li>
            <li class="nav-item">
                <strong><a class="nav-link" href="logout.php"><i class="fa-solid fa-door-open"></i> Log Out</a></strong>
            </li>
        </ul>
    </div>
</nav>
