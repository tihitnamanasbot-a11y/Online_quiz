
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Online Quiz</title>
</head>

<body>
<?php
$conn = new mysqli("localhost", "root", "", "online_quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password =
    password_hash($_POST["password"], 
    PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (name, email, username, password)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $username, $password);

    if ($stmt->execute()) {
        $message = "Registration successful!";
    } else {
        $message = "Registration failed: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<h1>Create Account</h1>

<form method="POST">

    <label>Full Name:</label><br>
    <input type="text" name="name" required>
    <br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required>
    <br><br>

    <label>Username:</label><br>
    <input type="text" name="username" required>
    <br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Register</button>

</form>

<p><?php echo $message; ?></p>

<br>

<a href="index.php">Back to Home</a>

</body>
</html>