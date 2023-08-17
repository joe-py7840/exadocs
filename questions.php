<?php
// Establish database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "exadocs";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the username is stored in the session
session_start();
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    // If the username is not set, redirect to the login page
    header("Location: index.php");
    exit();
}

// Retrieve the questions and answers uploaded by the user
$sql = "SELECT q.question, q.answer FROM questions q
        INNER JOIN users u ON q.user_id = u.id
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Questions</h1>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row["question"]; ?></td>
                    <td><?php echo $row["answer"]; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>