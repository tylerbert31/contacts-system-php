<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Contacts</h1>
        <?php
        // Get the email from the URL query parameter
        $email = isset($_GET['email']) ? urldecode($_GET['email']) : '';

        // Output the Add Contact form with the email as a hidden input field
        echo '<form action="addContact.php" method="GET">';
        echo '<input type="hidden" name="email" value="' . htmlspecialchars($email, ENT_QUOTES) . '">';
        echo '<button type="submit" class="btn btn-primary mt-3">Add Contact</button>';
        echo '</form>';
        ?>

        <!-- Add logout button that navigates to index.php -->
        <form action="index.php">
            <button type="submit" class="btn btn-secondary mt-3">Logout</button>
        </form>

        <?php
        // Establish a database connection
        $conn = new mysqli('localhost', 'root', '', 'baring');

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_GET['email'])) {
            $owner = urldecode($_GET['email']);
        } else {
            header('Location: index.php');
            exit;
        }

        // Check if $owner exists in the "users" table's "email" column
        $checkUserQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkUserQuery);
        $stmt->bind_param("s", $owner);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('Location: index.php');
            exit;
        }

        // Pagination settings
        $limit = 10; // Number of items per page
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page

        // Calculate the offset for the SQL query
        $offset = ($page - 1) * $limit;

        // Construct the SQL query with the condition and pagination
        $sql = "SELECT * FROM contacts WHERE owner = '$owner' LIMIT $limit OFFSET $offset";

        // Execute the query
        $result = $conn->query($sql);

        // Check if the query execution was successful
        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        // Check if there are rows returned
        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered mt-4">';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>Company</th>';
            echo '<th>Phone</th>';
            echo '<th>Email</th>';
            echo '<th>Modify</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            // Loop through the results and populate the table rows
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['company'] . '</td>';
                echo '<td>' . $row['phone'] . '</td>';
                echo '<td>' . $row['email'] . '</td>';
                echo '<td><button class="btn btn-danger">Delete</button></td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="alert alert-info mt-4">No contacts found for this owner.</div>';
        }

        // Pagination
        $paginationQuery = "SELECT COUNT(*) as total FROM contacts WHERE owner = '$owner'";
        $paginationResult = $conn->query($paginationQuery);
        $paginationData = $paginationResult->fetch_assoc();
        $totalPages = ceil($paginationData['total'] / $limit);

        echo '<nav aria-label="Page navigation">';
        echo '<ul class="pagination justify-content-center mt-4">';
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
            echo '<a class="page-link" href="?email=' . urlencode($email) . '&page=' . $i . '">' . $i . '</a>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</nav>';

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
