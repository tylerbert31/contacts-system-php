<?php
session_start();

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
  // Check if passwords match
  if ($_POST['password'] !== $_POST['confirm_password']) {
    $error = 'Passwords do not match';
  } else {
    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'baring');

    // Check if the email is already registered
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      // Email is already registered, show an error message
      $error = 'Email is already registered';
    } else {
      // Email is not registered, insert the new user into the database
      $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
      $stmt->bind_param('sss', $_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT));
      $stmt->execute();

      // Registration succeeded, redirect to the login page
      header('Location: index.php');
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <?php if (isset($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" name="name" id="name" required>
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" name="email" id="email" required>
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" class="form-control" name="password" id="password" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
      </div>

      <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <button class="btn btn-secondary" onclick="location.href='index.php'">Login</button>
  </div>

  <!-- Include Bootstrap JavaScript and jQuery (optional) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
