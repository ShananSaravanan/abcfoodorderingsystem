<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $chefName = $_POST['newChefName'];
    $email = $_POST['newChefEmail'];
    $password = password_hash($_POST['newChefPassword'], PASSWORD_DEFAULT);
    $contact = $_POST['newChefContact'];
    $vendorId = $_SESSION['vendor_id'];

    // Insert data into the chef table
    $query = "INSERT INTO chef (Chef_Name, email, password, Chef_Contact, vendor_id) VALUES ('$chefName', '$email', '$password', '$contact', '$vendorId')";
    
    if ($conn->query($query) === TRUE) {
        header('Location: chef_info.php'); // Redirect to the chefs page
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

$conn->close();
?>
