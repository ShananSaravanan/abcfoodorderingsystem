<?php
session_start(); // Start the session

include 'connection.php';

// Check if the vendor is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login_vendor.php'); // Redirect to login page if not logged in
    exit();
}

$vendor_id = $_SESSION['id'];

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
    <title>Vendor | Menus</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            /* Adjust the shadow as needed */
        }

        .custom-menu-image {

            /* Adjust the width as needed */
            height: 450px;
            /* Adjust the height as needed */
            object-fit: cover;
            /* Maintain aspect ratio and cover the entire container */
        }

        .dropdown-item:hover {
            background-color: #007bff;
            /* Blue color on hover, adjust as needed */
        }

        /* Adjust the title positions */
        .titles {
            margin-top: 30px;
        }

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
        <h2 class="titles">Your listed menus</h2>
        <input type="text" id="menuSearch" class="form-control mb-3" placeholder="Search by menu name">


        <div class="row">


            <?php while ($menu_row = $result->fetch_assoc()) : ?>
                <!-- Menu Card -->
                <div class="col-md-4 mb-4 menu-card">
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

                        <img src="<?php echo $menu_row['menu_img']; ?>" class="card-img-top custom-menu-image" alt="Menu Image">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo $menu_row['Item_Name']; ?></h5>
                            <p class="card-text"><?php echo $menu_row['Item_Description']; ?></p>
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
                                <!-- Add form elements for editing menu information -->
                                <form action="editmenu.php" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <input type="hidden" name="menuid" value="<?php echo $menu_row['id']; ?>">
                                        <label for="editItemName">Item Name</label>
                                        <input type="text" name="itemName" class="form-control" id="editItemName" value="<?php echo $menu_row['Item_Name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="editDescription">Description</label>
                                        <textarea name="itemDescription" class="form-control" id="editDescription" rows="3"><?php echo $menu_row['Item_Description']; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="editPrice">Price</label>
                                        <input type="text" name="itemPrice" class="form-control" id="editPrice" value="<?php echo $menu_row['Price']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="editItemImage">Select Image</label>
                                        <input type="file" name="editItemImage" class="form-control-file" id="editItemImage" accept="image/*" onchange="previewEditImage(this, <?php echo $menu_row['id']; ?>);">

                                        <img id="editImagePreview<?php echo $menu_row['id']; ?>" class="mt-2" style="max-width: 100%;" alt="Image Preview" src="<?php echo $menu_row['menu_img']; ?>">

                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
            <div class="floating-button">
                <div class="tooltip-container">
                    <button class="btn btn-primary btn-lg rounded-circle" data-toggle="modal" data-target="#addMenuModal">
                        <i class="fas fa-plus"></i>
                    </button>
                    <span class="tooltip-text">Add New Menu Item</span>
                </div>
            </div>
            <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addMenuModalLabel">Add New Menu</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Add form elements for adding a new menu -->
                            <form action="addmenu.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="newItemName">Item Name</label>
                                    <input type="text" name="newItemName" class="form-control" id="newItemName" required>
                                </div>
                                <div class="form-group">
                                    <label for="newDescription">Description</label>
                                    <textarea name="newItemDescription" class="form-control" id="newDescription" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="newPrice">Price</label>
                                    <input type="text" name="newItemPrice" class="form-control" id="newPrice" required>
                                </div>
                                <div class="form-group">
                                    <label for="newItemImage">Select Image</label>
                                    <input type="file" name="newItemImage" class="form-control-file" id="newItemImage" accept="image/*" onchange="previewImage(this);">
                                    <img id="imagePreview" class="mt-2" style="max-width: 100%;" alt="Image Preview">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Menu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function previewEditImage(input, menuId) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#editImagePreview' + menuId).attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }

        function resetEditImage(menuId) {
            // Reset the image source to the original value
            var originalSrc = $('#editImagePreview' + menuId).data('original-src');
            $('#editImagePreview' + menuId).attr('src', originalSrc);
        }

        $(document).ready(function() {
            // Handle modal show event
            $('.modal').on('show.bs.modal', function() {
                // Get the modal ID
                var modalId = $(this).attr('id');

                // Extract menuId from modalId
                var menuId = modalId.replace('editModal', '');

                // Save the original image source
                var originalSrc = $('#editImagePreview' + menuId).attr('src');
                $('#editImagePreview' + menuId).data('original-src', originalSrc);
            });

            // Handle modal close event
            $('.modal').on('hidden.bs.modal', function() {
                // Get the modal ID
                var modalId = $(this).attr('id');

                // Extract menuId from modalId
                var menuId = modalId.replace('editModal', '');

                // Reset the image source if it's an edit modal
                if (modalId.startsWith('editModal')) {
                    resetEditImage(menuId);
                }
            });

            $('#menuSearch').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();

                $('.menu-card').each(function() {
                    var menuName = $(this).find('.card-title').text().toLowerCase();

                    if (menuName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

        function previewImage(input) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        // Open the dropdown when three dots icon is clicked
        $('.dropdown-menu-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var dropdown = $(this).find('.dropdown-menu');
            dropdown.toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
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
