<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $chefId = $_POST['chefid'];
    $chefName = $_POST['editChefName'];
    $email = $_POST['editChefEmail'];
    $contact = $_POST['editChefContact'];

    // Update data in the chef table
    $query = "UPDATE chef SET Chef_Name='$chefName', email='$email', Chef_Contact='$contact' WHERE id='$chefId'";
    
    if ($conn->query($query) === TRUE) {
        header('Location: chef_info.php'); // Redirect to the chefs page
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
