<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="login-body">
  
    
      <h1 class="login-header">Register</h1>
      <?php if (isset($_SESSION['error'])): ?>
        <div class="error" role="alert">
          <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']); // Clear the error after displaying it
          ?>
        </div>
      <?php endif; ?>
      <div class="form-box login">
      <form action="../actions/register_action.php" method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label><br>
          <input type="text" name="username" class="my-description" id="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label><br>
          <input type="password" name="password" class="my-description" id="password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label><br>
          <input type="password" name="confirm_password" class="my-description" id="confirm_password" required>
        </div>
        <button type="submit" class="create-task register-button">Register</button>
      </form>
    </div>
  
</body>

</html>