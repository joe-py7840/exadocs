<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exadocs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = $_POST['subject'];
    $questionToEdit = $_POST['questionToEdit'] ?? '';
    $question = $_POST['question'] ?? '';
    $answer = $_POST['answer'] ?? '';
    $marks = $_POST['marks'] ?? '';

    $subjectTable = strtolower(str_replace(" ", "_", $subject));

    // Update the question and answer in the database
    $stmt = $conn->prepare("UPDATE $subjectTable SET question = ?, answer = ?, marks = ? WHERE question = ?");
    $stmt->bind_param("ssis", $question, $answer, $marks, $questionToEdit);
    $result = $stmt->execute();

    if ($result) {
        // Redirect back to the main page
        header("Location: about.php?subject=" . urlencode($subject));
        exit();
    } else {
        echo "Error updating question: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>