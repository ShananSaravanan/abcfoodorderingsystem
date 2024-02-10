<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve table name from the session (assuming it's set during login)
$tableName = $_SESSION['table_name'];

// Retrieve user information based on user ID and table name
$userID = $_SESSION['id'];
$sql = "SELECT * FROM $tableName WHERE id = $userID";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo 'Error fetching user information';
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    // (You may want to add more validation based on your requirements)

    // Dynamically update user attributes
    foreach ($user as $attribute => $value) {
        // Skip the 'id' field and any other fields you want to exclude
        if ($attribute != 'id') {
            $newValue = isset($_POST[$attribute]) ? $_POST[$attribute] : '';
            $newValue = mysqli_real_escape_string($conn, $newValue);

            $updateSql = "UPDATE $tableName SET $attribute = '$newValue' WHERE id = $userID";
            $conn->query($updateSql);
        }
    }

    // Redirect back to the account page after updating
    header("Location: accountinfo.php?updateSuccess=true");
    exit();
}

$updateSuccess = isset($_GET['updateSuccess']) && $_GET['updateSuccess'] === 'true';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>

    <!-- Bootstrap CSS (CDN link) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/16c00b39bb.js" crossorigin="anonymous"></script>
</head>

<body>
<?php include $_SESSION['nav']; ?>;
    <div class="container mt-5">
        
        <br>
        <?php if ($updateSuccess) : ?>
            <!-- Display success alert -->
            <div id="successMessage" class="alert alert-success" role="alert">
                Your information has been successfully updated.
            </div>
        <?php endif; ?>
        <?php unset($_GET['updateSuccess']); ?>
        <h2>Your Account</h2>

        <form method="POST" action="accountinfo.php" class="mt-3">
            <?php
            // Dynamically generate form fields for each user attribute
            foreach ($user as $attribute => $value) {
                // Skip the 'id' field and any other fields you want to exclude
                if ($attribute != 'id') {
                    echo '<div class="mb-3">';
                    echo '<label for="' . $attribute . '" class="form-label">' . ucfirst($attribute) . ':</label>';
                    echo '<input type="text" name="' . $attribute . '" value="' . $value . '" class="form-control" required>';
                    echo '</div>';
                }
            }
            ?>
            <button type="submit" class="btn btn-primary">Update Information</button>
        </form>
    </div>

    <!-- Bootstrap JS (CDN link) - Optional, only if you need Bootstrap JavaScript features -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to hide the success message after a delay
        function hideSuccessMessage() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function () {
                    successMessage.style.display = 'none';
                }, 3000); // Adjust the time in milliseconds (5000 = 5 seconds)
            }
        }

        // Call the function when the page loads
        document.addEventListener("DOMContentLoaded", function () {
            hideSuccessMessage();
        });
    </script>
</body>

</html>


