<!-- Main Screen which incorporates distinct user authentication features -->
<?php
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering System - Vendor Registration</title>
    <link rel="stylesheet" href="style.css">
</head>

<!-- Body -->
<body>
<!-- Header -->
<header>
    <!-- Left Section -->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>

    <!-- Right Section -->
    <nav>
        <ul>
            <?php
            // Check if the user is logged in and display appropriate links
            $isLoggedIn = true; // Replace this with your actual logic to check if the user is logged in
            if ($isLoggedIn) {
                echo '<li><a href="#">Log Out</a></li>';
            } else {
                echo '<li><a href="#">Login</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>

<!-- Register Form -->
<form action="register_process.php" method="post">
    <h2>Register</h2>
    <label for="reg_name">Name</label>
    <input type="text" id="reg_name" name="reg_name" required>

    <label for="reg_email">Email:</label>
    <input type="email" id="reg_email" name="reg_email" required>

    <label for="reg_password">Password:</label>
    <input type="password" id="reg_password" name="reg_password" required>

    <label for="reg_contact">Contact:</label>
    <input type="text" id="reg_contact" name="reg_contact">

    <button type="submit">Register</button>
</form>

<!-- Footer -->
<footer>
    &copy; 2024 Waffle Division
</footer>

</body>
</html>