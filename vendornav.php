<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <p class="navbar-brand"><?php echo $vendor_name; ?>'s Vendor Dashboard</p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'vendor_dashboard.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="vendor_dashboard.php">Menus</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'customer_order.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="customer_order.php">Orders</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'chef_info.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="chef_info.php">Chefs</a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'deliveryhistory.php' ? 'active' : ''; ?>">
                <a class="nav-link" href="deliveryhistory.php">Delivery Information</a>
            </li>
            <li class="nav-item">
                <strong><a class="nav-link" href="logout.php"><i class="fa-solid fa-door-open"></i> Log Out</a></strong>
            </li>
        </ul>
    </div>
</nav>
