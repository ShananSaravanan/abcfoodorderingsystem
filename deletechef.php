<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['chefid'])) {
    $chefId = $_GET['chefid'];

    // Delete chef from the chef table
    $query = "DELETE FROM chef WHERE id='$chefId'";
    
    if ($conn->query($query) === TRUE) {
        header('Location: chef_info.php'); // Redirect to the chefs page
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
?>
