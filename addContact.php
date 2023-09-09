<!DOCTYPE html>
<html>
<head>
    <title>Add Contact</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <h1>Add Contact</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get user input from the form
        $name = $_POST['name'];
        $company = $_POST['company'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        // Retrieve the owner from the URL parameter
        if (isset($_GET['email'])) {
            $owner = urldecode($_GET['email']);
        } else {
            $owner = ""; // Set owner to blank if email parameter is missing
        }

        // Check if $owner is blank and navigate to index.php
        if (empty($owner)) {
            header("Location: index.php");
            exit(); // Make sure to exit to prevent further script execution
        }

        // Establish a MySQL database connection
        $conn = new mysqli('localhost', 'root', '', 'baring');

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the email or name already exists in the database
        $checkQuery = "SELECT COUNT(*) FROM contacts WHERE (name = ? OR email = ?) AND owner = ?";
        $stmtCheck = $conn->prepare($checkQuery);
        $stmtCheck->bind_param("sss", $name, $email, $owner);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close(); // Close the check statement

        if ($count > 0) {
            echo "Error: The name or email already exists in the database.";
        } else {
            // Prepare and execute the SQL query to insert the data into the database
            $insertQuery = "INSERT INTO contacts (name, company, phone, email, owner) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->bind_param("sssss", $name, $company, $phone, $email, $owner);

            if ($stmtInsert->execute()) {
                // Redirect to contacts.php on successful INSERT
                header("Location: contacts.php?email=$owner");
                exit(); // Make sure to exit to prevent further script execution
            } else {
                echo "Error: " . $insertQuery . "<br>" . $conn->error;
            }

            $stmtInsert->close(); // Close the insert statement
        }

        // Close the database connection
        $conn->close();
    }
    ?>

    <form class="container mt-5" action="addContact.php<?php if (isset($_GET['email'])) { echo '?email=' . urlencode($_GET['email']); } ?>" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="form-group">
            <label for="company">Company:</label>
            <input type="text" class="form-control" name="company" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" name="phone" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <button type="submit" class="btn btn-primary" name="submit">Add Contact</button>

        <!-- Cancel button that goes back to the previous page -->
        <a class="btn btn-secondary" href="javascript:history.go(-1)">Cancel</a>
    </form>

    <!-- Include Bootstrap JavaScript and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
