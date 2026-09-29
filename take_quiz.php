<?php
session_start();

$conn = new mysqli("localhost", "root", "", "online_quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_GET['quiz_id'])) {
    die("Quiz ID is missing!");
}

$quiz_id = (int)$_GET['quiz_id'];

$sql = "SELECT id, title, description FROM quizzes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();

$result = $stmt->get_result();
$quiz = $result->fetch_assoc();

$stmt->close();

if (!$quiz) {
    die("Quiz not found!");
}

$sql = "SELECT id, question_text, option_a, option_b, option_c, option_d
        FROM questions
        WHERE quiz_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();

$questions = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
</head>

<body>

<h1><?php echo htmlspecialchars($quiz['title']); ?></h1>

<p><?php echo htmlspecialchars($quiz['description']); ?></p>

<?php if ($questions->num_rows > 0): ?>

<form method="POST" action="submit_quiz.php">

    <input type="hidden" name="quiz_id"
           value="<?php echo $quiz_id; ?>">

    <?php
    $number = 1;

    while ($question = $questions->fetch_assoc()) {
    ?>

        <h3>
            <?php
            echo $number . ". " .
                 htmlspecialchars($question['question_text']);
            ?>
        </h3>

        <label>
            <input type="radio"
                   name="answer[<?php echo $question['id']; ?>]"
                   value="A"
                   required>
            <?php echo htmlspecialchars($question['option_a']); ?>
        </label>
        <br>

        <label>
            <input type="radio"
                   name="answer[<?php echo $question['id']; ?>]"
                   value="B">
            <?php echo htmlspecialchars($question['option_b']); ?>
        </label>
        <br>

        <label>
            <input type="radio"
                   name="answer[<?php echo $question['id']; ?>]"
                   value="C">
            <?php echo htmlspecialchars($question['option_c']); ?>
        </label>
        <br>

        <label>
            <input type="radio"
                   name="answer[<?php echo $question['id']; ?>]"
                   value="D">
            <?php echo htmlspecialchars($question['option_d']); ?>
        </label>

        <hr>

    <?php
        $number++;
    }
    ?>

    <button type="submit">Submit Quiz</button>

</form>

<?php else: ?>

<p>No questions found for this quiz.</p>

<?php endif; ?>

<br>

<a href="index.php">Back to Home</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>