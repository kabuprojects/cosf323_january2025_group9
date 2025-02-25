<?php
session_start();
require "db.php";

if (!isset($_SESSION["pending_user"]) || !isset($_SESSION["security_question"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["pending_user"];
$security_question = $_SESSION["security_question"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer = $_POST["security_answer"];

    $stmt = $conn->prepare("SELECT security_answer_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($security_answer_hash);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($answer, $security_answer_hash)) {
        $_SESSION["user"] = $username;
        unset($_SESSION["pending_user"], $_SESSION["security_question"], $_SESSION["attempts"]);

        echo "<div class='container'>";
        echo "<h2 class='success'>✅ Login successful! Welcome, $username.</h2>";
        echo '<a href="logout.php" class="btn logout">Logout</a>';
        echo "</div>";
        exit();
    } else {
        if (!isset($_SESSION["attempts"])) {
            $_SESSION["attempts"] = 0;
        }
        $_SESSION["attempts"]++;

        if ($_SESSION["attempts"] >= 3) {
            echo "<div class='container'><h2 class='error'>❌ Too many failed attempts! Please try again later.</h2></div>";
            session_destroy();
            exit();
        } else {
            echo "<div class='container'><h2 class='error'>❌ Incorrect answer. " . (3 - $_SESSION["attempts"]) . " attempt(s) left.</h2></div>";
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Check</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($security_question); ?></h2>
        <form method="POST">
            <label>Answer:</label>
            <input type="text" name="security_answer" required>
            <button type="submit">Submit</button>
        </form>
        <a href="logout.php" class="btn logout">Logout</a>
    </div>
</body>
</html>
