<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exadocs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedSubject = $_POST["subject"];
    $questionToDelete = $_POST["questionToDelete"];

    $subjectTable = strtolower(str_replace(" ", "_", $selectedSubject));

    $query = "DELETE FROM $subjectTable WHERE question = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $questionToDelete);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: about.php?subject=" . urlencode($selectedSubject));
        exit();
    } else {
        $stmt->close();
        $conn->close();
        echo "Error: " . $conn->error;
    }
}
?>
