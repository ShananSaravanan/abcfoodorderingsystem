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
    <?php include 'headermenus.php'; ?>
    <div class="chef-section ml-lg-3">
        <ul class="nav">
          <li class="nav-item"><a href="#" class="nav-link text-white" style="border: 1px solid white;">CHEF</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

<div class="main_page">
  <div class="content text-white">
    <h1>ABC System</h1>
    <div class="container">
      <div class="row">
        <div class="col-md-6 mx-auto mt-5">
          <div class="form-container">
            <ul class="nav nav-tabs" id="myTabs">
              <li class="nav-item">
                <a class="nav-link active" id="login-tab" data-bs-toggle="tab" href="#login">Login</a>
              </li>
              
            </ul>

            <div class="tab-content mt-2">
              <div class="tab-pane fade show active" id="login">
              <form action="login.php" method="post">
                        <input type="hidden" name="redirect_page" value="chef_dashboard.php">
                        <input type="hidden" name="from_page" value="login_chef.php">
                        <input type="hidden" name="tablename" value="chef">
                  <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" name="loginEmail" id="loginEmail" required>
                  </div>
                  <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" name="loginPassword" id="loginPassword" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Login</button>
                </form>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="js/jquery-3.5.1.slim.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>