<?php
$conn = new mysqli("localhost", "root", "", "online_quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

// Get quizzes
$quizzes = $conn->query("SELECT id, title FROM quizzes");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $quiz_id = $_POST["quiz_id"];
    $question_text = $_POST["question_text"];
    $option_a = $_POST["option_a"];
    $option_b = $_POST["option_b"];
    $option_c = $_POST["option_c"];
    $option_d = $_POST["option_d"];
    $correct_answer = $_POST["correct_answer"];

    $sql = "INSERT INTO questions
            (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "issssss",
        $quiz_id,
        $question_text,
        $option_a,
        $option_b,
        $option_c,
        $option_d,
        $correct_answer
    );

    if ($stmt->execute()) {
        $message = "Question added successfully!";
    } else {
        $message = "Question adding failed: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question - Online Quiz</title>
</head>

<body>

<h1>Add Question</h1>

<form method="POST">

    <label>Select Quiz:</label><br>

    <select name="quiz_id" required>
        <option value="">-- Select Quiz --</option>

        <?php while ($quiz = $quizzes->fetch_assoc()) { ?>
            <option value="<?php echo $quiz['id']; ?>">
                <?php echo htmlspecialchars($quiz['title']); ?>
            </option>
        <?php } ?>

    </select>

    <br><br>

    <label>Question:</label><br>
    <textarea name="question_text" required></textarea>

    <br><br>

    <label>Option A:</label><br>
    <input type="text" name="option_a" required>

    <br><br>

    <label>Option B:</label><br>
    <input type="text" name="option_b" required>

    <br><br>

    <label>Option C:</label><br>
    <input type="text" name="option_c" required>

    <br><br>

    <label>Option D:</label><br>
    <input type="text" name="option_d" required>

    <br><br>

    <label>Correct Answer:</label><br>

    <select name="correct_answer" required>
        <option value="">-- Select Correct Answer --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
    </select>

    <br><br>

    <button type="submit">Add Question</button>

</form>

<p>
    <?php echo $message; ?>
</p>

<br>

<a href="index.php">Back to Home</a>

</body>
</html>