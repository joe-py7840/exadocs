<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If the user is not logged in, redirect to the login page or display an error message
    header('Location: index.php');
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];

// Array of subjects and corresponding table names
$subjectTables = array(
    "Maths" => "maths",
    "English" => "english",
    "Kiswahili" => "kiswahili",
    "Physics" => "physics",
    "Chemistry" => "chemistry",
    "Biology" => "biology",
    "Geography" => "geography",
    "History" => "history",
    "CRE" => "cre",
    "Computer Studies" => "computer_studies",
    "Agriculture" => "agriculture",
    "Business Studies" => "business_studies"
);

// Database connection configuration
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "exadocs";

// Create a new connection
$conn = new mysqli($servername, $db_username, $db_password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected subject from the form
    $selectedSubject = $_POST['subject'];
    
    // Get the table name for the selected subject
    $tableName = $subjectTables[$selectedSubject];

    // Get the question, answer, and marks arrays from the form
    $questions = $_POST['question'];
    $answers = $_POST['answer'];
    $marks = $_POST['marks'];

    // Prepare and execute the SQL query to insert the data into the respective table
    $stmt = $conn->prepare("INSERT INTO $tableName (question, answer, marks) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $question, $answer, $mark);

    // Insert each question, answer, and marks into the table
    for ($i = 0; $i < count($questions); $i++) {
        $question = $questions[$i];
        $answer = $answers[$i];
        $mark = $marks[$i];
        $stmt->execute();
    }

    // Close the prepared statement
    $stmt->close();

    // Set a success message
    $successMessage = "Upload successful!";

    // Redirect back to the same page to avoid form resubmission
    // header('Location: home.php');
    // exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="home.php">Admin</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="about.php">View Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <h2>Select Subject</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="mb-3">
            <label for="subjectSelect" class="form-label">Subject:</label>
            <select class="form-select" id="subjectSelect" name="subject">
                <?php
                // Generate options for the subject select dropdown
                foreach ($subjectTables as $subject => $tableName) {
                    echo '<option value="' . $subject . '">' . $subject . '</option>';
                }
                ?>
            </select>
        </div>

        <h2>Question List</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Question</th>
                <th>Answer</th>
                <th>Marks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="text" class="form-control" name="question[]"></td>
                <td><input type="text" class="form-control" name="answer[]"></td>
                <td><input type="number" class="form-control" name="marks[]"></td>
            </tr>
            <!-- Add more rows dynamically using JavaScript if needed -->
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo isset($successMessage) ? $successMessage : ''; ?>
                </div>
                <div class="modal-footer">
                <button type="button" id="successModalOK" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Show success modal if success message is set -->
<?php if (isset($successMessage)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            var okButton = document.getElementById('successModalOK');
            okButton.addEventListener('click', function() {
                successModal.hide();
            });
        });
    </script>
<?php endif; ?>
</body>
</html>