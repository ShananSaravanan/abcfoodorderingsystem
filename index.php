<?php include 'connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link rel="stylesheet" href="./css/style.css">
  <title>Food Ordering System - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="p-3 bg-dark text-white">
<div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <ul class="nav nav-pills">
          <li class="nav-item"><a href="#" class="nav-link active bg-secondary text-white" aria-current="page">Home</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">About Us</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Contact</a></li>
        </ul>
      </div>
    </div>
</header>

<div class="main_page">
  <div class="content text-white">
    <h1>ABC System</h1>
    <br>
    <h4>Welcome to our system. <br> Please choose your login option below:</h4>

    <div class="login-options mt-3 d-flex justify-content-center">
      <a href="login_customer.php" class="icon-box">
        <i class="fas fa-user"></i>
        <span>Customer Login</span>
      </a>
      <a href="login_vendor.php" class="icon-box ml-3">
        <i class="fa fa-shopping-cart"></i>
        <span>Vendor Login</span>
      </a>
      <a href="login_delivery.php" class="icon-box ml-3">
        <i class="fa fa-car"></i>
        <span>Delivery Login</span>
      </a>
      <a href="login_chef.php" class="icon-box ml-3">
        <i class="fa fa-cutlery"></i>
        <span>Chef Login</span>
      </a>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="js/jquery-3.5.1.slim.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>