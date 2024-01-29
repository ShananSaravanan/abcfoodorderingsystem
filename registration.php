<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try{
    $table = $_POST["tablename"];
    $page = $_POST["redirect_page"];
    $name = $_POST["registerName"];
    $email = $_POST["registerEmail"];
    $password = password_hash($_POST["registerPassword"], PASSWORD_DEFAULT);
    $contact = isset($_POST['registerContact']) ? $_POST['registerContact'] : 'empty';
    $vehicleinfo = isset($_POST['vehicleinfo']) ? $_POST['vehicleinfo'] : 'empty';
        
    if($contact!="empty"){
        
        $sql = "INSERT INTO $table VALUES ('$name', '$email', '$password','$contact')";
    }
    else if ($vehicleinfo != "empty"){
        
        $sql = "INSERT INTO $table VALUES ('$name', '$email', '$password','$contact','$vehicleinfo')";
        
    }
    else{
        $sql = "INSERT INTO $table VALUES ('$name', '$email', '$password')";
    }
    // Insert data into the database
    if ($conn->query($sql) == TRUE) {
        // Registration successful
        
        $redirectPage = isset($_POST['redirect_page']) ? $_POST['redirect_page'] : 'index.php';
        echo "<script>alert('Registration successful!'); window.location.href='$page';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} catch (Exception $e) {
    echo "<script>alert($e); window.location.href='login_vendor.php';</script>";
}
}
?>
