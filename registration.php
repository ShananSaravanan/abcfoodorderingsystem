<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $table = $_POST["tablename"];
        $page = $_POST["redirect_page"];
        $name = $_POST["registerName"];
        $email = $_POST["registerEmail"];
        $password = password_hash($_POST["registerPassword"], PASSWORD_DEFAULT);
        $contact = isset($_POST['registerContact']) ? $_POST['registerContact'] : 'empty';
        $vehicleinfo = isset($_POST['vehicleinfo']) ? $_POST['vehicleinfo'] : 'empty';
        $address = isset($_POST['address']) ? $_POST['address'] : 'empty';

        // Get column names from the table excluding 'id'
        $result = $conn->query("DESCRIBE $table");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] != 'id') {
                $columns[] = $row['Field'];
            }
        }

        // Initialize the query with common fields
        $sql = "INSERT INTO $table (";

        // Add column names based on conditions
        $sql .= implode(', ', $columns);

        // Complete the query
        $sql .= ") VALUES (";

        // Add values for common fields
        $sql .= "'$name', '$email', '$password'";

        // Add values for optional fields based on conditions
        if ($contact != "empty") {
            $sql .= ", '$contact'";
        }
        if ($vehicleinfo != "empty") {
            $sql .= ", '$vehicleinfo'";
        }
        if ($address != "empty") {
            $sql .= ", '$address'";
        }

        $sql .= ")";

        // Log or print the query
        error_log($sql);  // Log to error log
        echo "Query: $sql<br>";  // Print to screen for debugging

        // Insert data into the database
        if ($conn->query($sql) == TRUE) {
            // Registration successful
            echo "<script>alert('Registration successful!'); window.location.href='$page';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } catch (Exception $e) {
        echo "<script>alert($e); window.location.href='index.php';</script>";
    }
}
?>
