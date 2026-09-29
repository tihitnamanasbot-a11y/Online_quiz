<?php
$conn = new mysqli("localhost", "root", "", "online_quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];

    $sql = "INSERT INTO quizzes (title, description)
            VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $description);

    if ($stmt->execute()) {
        $message = "Quiz created successfully!";
    } else {
        $message = "Quiz creation failed: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Quiz - Online Quiz</title>
</head>

<body>

<h1>Create Quiz</h1>

<form method="POST">

    <label>Quiz Title:</label><br>
    <input type="text" name="title" required>

    <br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea>

    <br><br>

    <button type="submit">Create Quiz</button>

</form>

<p><?php echo $message; ?></p>

<br>

<a href="index.php">Back to Home</a>

</body>
</html>