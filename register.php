<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $question = $_POST["security_question"];
    $answer = password_hash($_POST["security_answer"], PASSWORD_DEFAULT);

    //SQL statement to insert user data
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, security_question, security_answer_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $question, $answer);

    if ($stmt->execute()) {
        echo "<p class='success'>✅ Registration successful! <a href='login.php'>Login here</a></p>";
    } else {
        echo "<p class='error'>❌ Registration failed: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Security Question:</label>
            <select name="security_question" required>
                <option value="What is your favorite color?">What is your favorite color?</option>
                <option value="What is your pet’s name?">What is your pet’s name?</option>
                <option value="What city were you born in?">What city were you born in?</option>
                <option value="What is your mother’s maiden name?">What is your mother’s maiden name?</option>
                <option value="What is your first school?">What is your first school?</option>
            </select>

            <label>Security Answer:</label>
            <input type="text" name="security_answer" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php" class="btn">Login here</a></p>
    </div>
</body>
</html>
