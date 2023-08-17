<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exadocs";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if email is already used
    $emailExists = false;
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $emailExists = true;
    }
    $stmt->close();

    // Validate password requirements
    $passwordRequirementsMet = preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){6,}$/', $password);

    if ($emailExists) {
        die("Email already exists.");
    } elseif (!$passwordRequirementsMet) {
        echo "<script>alert('Password should be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');</script>";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        // Create a prepared statement
        $stmt = $conn->prepare($sql);

        // Bind the parameters with the form data
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        // Execute the statement
        $stmt->execute();

        // Close the statement
        $stmt->close();

        // Redirect to a success page or perform any additional actions
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Signup</h1>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Signup</button>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>

    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>