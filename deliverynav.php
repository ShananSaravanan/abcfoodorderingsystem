<?php
include "connection.php";
$delivery_id = $_SESSION['id'];

// Fetch menus for the logged-in vendor
$result = $conn->query("SELECT * FROM deliverypersonnel WHERE id = $delivery_id");


$row = $result->fetch_assoc();
$personnelname = $row['Personnel_Name'];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <p class="navbar-brand"><?php echo $personnelname; ?>'s Delivery Dashboard</p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'delivery_dashboard.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="delivery_dashboard.php">Orders</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'accountinfo.php' ? 'active' : ''; ?>">
            
                <a class="nav-link" href="accountinfo.php"><i class="fa-regular fa-user"></i> Account Information</a>
            </li>
            <li class="nav-item">
                <strong><a class="nav-link" href="logout.php"><i class="fa-solid fa-door-open"></i> Log Out</a></strong>
            </li>
        </ul>
    </div>
</nav>
