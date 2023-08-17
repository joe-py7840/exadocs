<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exadocs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$subjects = array(
    "Maths", "English", "Kiswahili", "Physics", "Chemistry", 
    "Biology", "Geography", "History", "CRE", "Computer Studies",
    "Agriculture", "Business Studies"
);

$selectedSubject = isset($_GET['subject']) ? $_GET['subject'] : '';

$questions = array();
$answers = array();
$marks = array();

if ($selectedSubject !== '') {
    $subjectTable = strtolower(str_replace(" ", "_", $selectedSubject));
    $query = "SELECT question, answer, marks FROM $subjectTable";
    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row['question'];
            $answers[] = $row['answer'];
            $marks[] = $row['marks']; // Populate the marks array
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Add custom styles for links */
        .subject-list li a {
            text-decoration: none; /* Remove underline */
            color: green; /* Set default color */
        }
        .subject-list li a.active {
            color: red; /* Set color for active link */
        }
    </style>
    <title>Subjects and Questions</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <h2>Select a Subject</h2>
    <div class="row">
        <div class="col-md-4">
            <ul class="list-group subject-list">
                <?php foreach ($subjects as $subject): ?>
                    <li class="list-group-item">
                        <a href="?subject=<?php echo urlencode($subject); ?>" class="<?php echo ($selectedSubject === $subject) ? 'active' : ''; ?>">
                            <?php echo $subject; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-8">
            <?php if ($selectedSubject !== ''): ?>
                <h3><?php echo $selectedSubject; ?></h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Marks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < count($questions); $i++): ?>
                            <tr>
                                <td><?php echo $questions[$i]; ?></td>
                                <td><?php echo $answers[$i]; ?></td>
                                <td><?php echo $marks[$i]; ?></td>
                                <td>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $i; ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $i; ?>">
                                        Delete                                    </button>
                                    <div class="modal fade" id="editModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $i; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?php echo $i; ?>">Edit Question and Answer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="process_upload.php" method="post">
                                                        <div class="mb-3">
                                                            <label for="question<?php echo $i; ?>" class="form-label">Question</label>
                                                            <textarea class="form-control" id="question<?php echo $i; ?>" name="question" rows="3"><?php echo $questions[$i]; ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="answer<?php echo $i; ?>" class="form-label">Answer</label>
                                                            <textarea class="form-control" id="answer<?php echo $i; ?>" name="answer" rows="3"><?php echo $answers[$i]; ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="marks<?php echo $i; ?>" class="form-label">Marks</label>
                                                            <input type="number" class="form-control" id="marks<?php echo $i; ?>" name="marks" value="<?php echo $marks[$i]; ?>">
                                                        </div>
                                                        <input type="hidden" name="subject" value="<?php echo $selectedSubject; ?>">
                                                        <input type="hidden" name="questionToEdit" value="<?php echo $questions[$i]; ?>">
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="deleteModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $i; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $i; ?>">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this question and answer?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="delete_question.php" method="post">
                                                        <input type="hidden" name="subject" value="<?php echo $selectedSubject; ?>">
                                                        <input type="hidden" name="questionToDelete" value="<?php echo $questions[$i]; ?>">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>