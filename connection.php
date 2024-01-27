<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "food_ordering_system";

//Create a connection to MySQL database with the credentials above
$conn = new mysqli($servername, $username, $password, $database);

// Check if connection to MySQL was successful, and return error if failed.
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>