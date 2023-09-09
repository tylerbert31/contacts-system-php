<?php
session_start();

if (isset($_POST['email']) && isset($_POST['password'])) {
    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'baring');

    // Prepare the SQL statement
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the email exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if the password is valid
        if (password_verify($_POST['password'], $user['password'])) {
            // Authentication succeeded, set the session variable and redirect to the contacts.php with email as a query parameter
            $_SESSION['email'] = $user['email'];
            header('Location: contacts.php?email=' . urlencode($_SESSION['email']));
            exit;
        } else {
            // Authentication failed, show an error message
            $error = 'Invalid password';
        }
    } else {
        // Authentication failed, show an error message
        $error = 'Invalid email';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>

            <button type="submit" class="btn btn-primary mb-1">Login</button>
        </form>

        <form action="register.php">
            <button type="submit" class="btn btn-secondary">Register</button>
        </form>
    </div>

    <!-- Include Bootstrap JavaScript and jQuery (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  
</body>
</html>
