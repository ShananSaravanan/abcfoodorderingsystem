<?php
session_start(); // Start the session

include 'connection.php';

// Check if the vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header('Location: login_vendor.php'); // Redirect to login page if not logged in
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch menus for the logged-in vendor
$result = $conn->query("SELECT * FROM menu WHERE vendor_id = $vendor_id");

// Fetch vendor details (optional)
$vendor_result = $conn->query("SELECT * FROM vendor WHERE id = $vendor_id");
$vendor_row = $vendor_result->fetch_assoc();
$vendor_name = $vendor_row['Vendor_Name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
    <!-- Your custom CSS (if any) -->
    <style>
        /* Add your custom styles here */
        body {
            padding-top: 56px; /* Adjusted for fixed navbar height */
        }

        @media (min-width: 768px) {
            body {
                padding-top: 0;
            }
        }

        /* Add some margin or padding to create a gap between titles and navbar */
        h2, h3 {
            margin-top: 20px;
        }

        /* Add margin to the dropdown button */
        .dropdown-menu-btn {
            margin: 0;
            position: absolute;
            top: 0;
            right: 0;
        }

        /* Customize the appearance of the dropdown */
        #dropdownMenuButton {
            background-color: transparent;
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); /* Adjust the shadow as needed */
        }

        

        

        .dropdown-item:hover {
            background-color: #007bff; /* Blue color on hover, adjust as needed */
        }

        /* Adjust the title positions */
        .titles {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand" href="#">Vendor Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">View Menus</a>
            </li> -->
            <!-- Add more menu items as needed -->
        <!-- </ul>
    </div> -->
</nav>

<div class="container-fluid mt-4">
    <br>
    <h2 class="titles">Welcome, <?php echo $vendor_name; ?>! Here are your menus</h2>

    <div class="row">

        <?php while ($menu_row = $result->fetch_assoc()) : ?>
            <!-- Menu Card -->
            <div class="col-md-4 mb-4">
                <div class="card position-relative">
                    <!-- Dropdown Button -->
                    <div class="dropdown dropdown-menu-btn">
                         <button class="btn btn-secondary" id="dropdownMenuButton" type="button" data-toggle="modal" data-target="#editModal<?php echo $menu_row['id']; ?>">
                            <!-- Three Dots Icon -->
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-menu" href="#" data-menu-id="<?php echo $menu_row['id']; ?>"><i class="fas fa-pen"></i> Edit</a>

                            <a class="dropdown-item delete-menu" href="#" data-menu-id="<?php echo $menu_row['id']; ?>"><i class="fas fa-trash-alt"></i> Delete</a>
                        </div>
                    </div>
                    
                    <img src="../images/images.jpg" class="card-img-top" alt="Menu Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $menu_row['Item_Name']; ?></h5>
                        <p class="card-text"><?php echo $menu_row['Description']; ?></p>
                        <p class="card-text">Price: RM<?php echo $menu_row['Price']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $menu_row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Menu</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Add form elements for editing menu information -->
                            <form action="editmenu.php" method="post">
                                <div class="form-group">
                                <input type="hidden" name="menuid" value="<?php echo $menu_row['id']; ?>">
                                    <label for="editItemName">Item Name</label>
                                    <input type="text" name="itemName" class="form-control" id="editItemName" value="<?php echo $menu_row['Item_Name']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="editDescription">Description</label>
                                    <textarea name="itemDescription" class="form-control" id="editDescription" rows="3"><?php echo $menu_row['Description']; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="editPrice">Price</label>
                                    <input type="text" name="itemPrice" class="form-control" id="editPrice" value="<?php echo $menu_row['Price']; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Open the dropdown when three dots icon is clicked
    $('.dropdown-menu-btn').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var dropdown = $(this).find('.dropdown-menu');
        dropdown.toggleClass('show');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown-menu-btn').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });

    // Trigger modal when clicking on Edit in the dropdown
    $('.dropdown-menu .edit-menu').on('click', function(e) {
        e.preventDefault();
        var menuId = $(this).data('menu-id');
        $('#editModal' + menuId).modal('show');
    });

    // Handle delete menu
    $('.dropdown-menu .delete-menu').on('click', function(e) {
        e.preventDefault();
        var menuId = $(this).data('menu-id');
        var confirmation = confirm('Are you sure you want to delete this menu?');
        
        if (confirmation) {
            // Redirect to delete menu page with menuId
            window.location.href = 'deletemenu.php?menuid=' + menuId;
        }
    });
</script>





</body>
</html>



