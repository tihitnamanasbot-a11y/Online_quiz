[9/15/2026 5:55 AM] Tetu: http://localhost/online_quiz/take_quiz.php?quiz_id=1
[9/15/2026 5:58 AM] Tetu: <?php
session_start();

$conn = new mysqli("localhost", "root", "", "online_quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$quiz_id = isset($_POST["quiz_id"]) ? (int)$_POST["quiz_id"] : 0;

$answers = isset($_POST["answer"]) && is_array($_POST["answer"])
    ? $_POST["answer"]
    : array();

if ($quiz_id <= 0) {
    die("Invalid Quiz ID.");
}


/* Get logged-in user's ID */
$user_id = 0;

if (isset($_SESSION["user_id"])) {

    $user_id = (int)$_SESSION["user_id"];

} elseif (isset($_SESSION["username"])) {

    $username = $_SESSION["username"];

    $stmt = $conn->prepare(
        "SELECT id FROM users WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $user_id = (int)$user["id"];
    }

    $stmt->close();
}

if ($user_id <= 0) {
    die("Please login first.");
}


/* Get questions and correct answers */
$stmt = $conn->prepare(
    "SELECT id, correct_answer
     FROM questions
     WHERE quiz_id = ?"
);

$stmt->bind_param("i", $quiz_id);
$stmt->execute();

$result = $stmt->get_result();

$total_questions = $result->num_rows;
$score = 0;

while ($question = $result->fetch_assoc()) {

    $question_id = (int)$question["id"];

    $correct_answer = strtoupper(
        trim($question["correct_answer"])
    );

    $given_answer = "";

    if (isset($answers[$question_id])) {
        $given_answer = strtoupper(
            trim($answers[$question_id])
        );
    }

    if ($given_answer !== "" && $given_answer === $correct_answer) {
        $score++;
    }
}

$stmt->close();


/* Save result */
$stmt = $conn->prepare(
    "INSERT INTO results
    (user_id, quiz_id, score, total_questions, completed_at)
    VALUES (?, ?, ?, ?, NOW())"
);

$stmt->bind_param(
    "iiii",
    $user_id,
    $quiz_id,
    $score,
    $total_questions
);

if (!$stmt->execute()) {
    die("Failed to save result: " . $stmt->error);
}

$stmt->close();


/* Get quiz title */
$stmt = $conn->prepare(
    "SELECT title FROM quizzes WHERE id = ?"
);

$stmt->bind_param("i", $quiz_id);
$stmt->execute();

$result = $stmt->get_result();

$quiz_title = "Quiz";

if ($quiz = $result->fetch_assoc()) {
    $quiz_title = $quiz["title"];
}

$stmt->close();

$conn->close();


/* Calculate percentage */
$percentage = 0;

if ($total_questions > 0) {
    $percentage = ($score / $total_questions) * 100;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quiz Result</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
            padding: 50px;
        }

        .result-box {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h1 {
            color: #333;
        }

        .score {
            font-size: 28px;
            font-weight: bold;
            margin: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>

</head>

<body>

<div class="result-box">

    <h1>Quiz Completed!</h1>

    <h2><?php echo htmlspecialchars($quiz_title); ?></h2>

    <div class="score">
        Score: <?php echo $score; ?>
        /
        <?php echo $total_questions; ?>
    </div>

    <p>
        Percentage:
        <?php echo number_format($percentage, 2); ?>%
    </p>
[9/15/2026 5:58 AM] Tetu: <a class="button" href="take_quiz.php?quiz_id=<?php echo $quiz_id; ?>">
        Take Quiz Again
    </a>

    <a class="button" href="index.php">
        Back to Home
    </a>

</div>

</body>

</html>