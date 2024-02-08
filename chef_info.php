<?php
session_start(); // Start the session

include 'connection.php';

// Check if the vendor is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login_vendor.php'); // Redirect to login page if not logged in
    exit();
}

$vendor_id = $_SESSION['id'];

// Fetch chefs for the logged-in vendor
$result = $conn->query("SELECT * FROM chef WHERE vendor_id = $vendor_id");
$vendor_result = $conn->query("SELECT * FROM vendor WHERE id = $vendor_id");
$vendor_row = $vendor_result->fetch_assoc();
$vendor_name = $vendor_row['Vendor_Name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefs | Vendor</title>
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
        <h2 class="titles">List of Chefs</h2>
            <input type="text" class="form-control" id="chefSearch" placeholder="Search chefs">
        </div>
        <div class="row">
            <div class="col-md-12">
            <div class="col-md-6">
        
    </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Chef Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="chefTableBody">
                        <?php while ($chef_row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $chef_row['id']; ?></td>
                                <td><?php echo $chef_row['Chef_Name']; ?></td>
                                <td><?php echo $chef_row['email']; ?></td>
                                <td><?php echo $chef_row['Chef_Contact']; ?></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editChefModal<?php echo $chef_row['id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="deletechef.php?chefid=<?php echo $chef_row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this chef?')"><i class="fa-solid fa-trash"></i> Delete</a>
                                </td>
                            </tr>

                            <!-- Edit Chef Modal -->
                            <div class="modal fade" id="editChefModal<?php echo $chef_row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editChefModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editChefModalLabel">Edit Chef</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="editchef.php" method="post">
                                                <input type="hidden" name="chefid" value="<?php echo $chef_row['id']; ?>">
                                                <div class="form-group">
                                                    <label for="editChefName">Chef Name</label>
                                                    <input type="text" name="editChefName" class="form-control" id="editChefName" value="<?php echo $chef_row['Chef_Name']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editChefEmail">Email</label>
                                                    <input type="email" name="editChefEmail" class="form-control" id="editChefEmail" value="<?php echo $chef_row['email']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editChefContact">Contact</label>
                                                    <input type="text" name="editChefContact" class="form-control" id="editChefContact" value="<?php echo $chef_row['Chef_Contact']; ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="floating-button">
            <div class="tooltip-container">
                <button class="btn btn-primary btn-lg rounded-circle" data-toggle="modal" data-target="#addChefModal">
                    <i class="fas fa-plus"></i>
                </button>
                <span class="tooltip-text">Add New Chef</span>
            </div>
        </div>

        <!-- Add Chef Modal -->
        <div class="modal fade" id="addChefModal" tabindex="-1" role="dialog" aria-labelledby="addChefModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addChefModalLabel">Add New Chef</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="addchef.php" method="post">
                            <div class="form-group">
                                <label for="newChefName">Chef Name</label>
                                <input type="text" name="newChefName" class="form-control" id="newChefName" required>
                            </div>
                            <div class="form-group">
                                <label for="newChefEmail">Email</label>
                                <input type="email" name="newChefEmail" class="form-control" id="newChefEmail" required>
                            </div>
                            <div class="form-group">
                                <label for="newChefContact">Contact</label>
                                <input type="text" name="newChefContact" class="form-control" id="newChefContact" required>
                            </div>
                            <div class="form-group">
                                <label for="newChefPassword">Password</label>
                                <input type="password" name="newChefPassword" class="form-control" id="newChefPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Chef</button>
                        </form>
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
    $('#chefSearch').on('input', function () {
            var searchTerm = $(this).val().toLowerCase();

            // Loop through each chef row in the table body
            $('#chefTableBody tr').each(function () {
                var chefName = $(this).find('td:eq(1)').text().toLowerCase();
                var chefEmail = $(this).find('td:eq(2)').text().toLowerCase();
                var chefContact = $(this).find('td:eq(3)').text().toLowerCase();

                // Check if the chef's information contains the search term
                if (chefName.includes(searchTerm) || chefEmail.includes(searchTerm) || chefContact.includes(searchTerm)) {
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
