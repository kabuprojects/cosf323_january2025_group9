<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION["pending_user"];
    $answer = $_POST["security_answer"];

    $stmt = $conn->prepare("SELECT security_answer_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($security_answer_hash);
        $stmt->fetch();

        if (password_verify($answer, $security_answer_hash)) {
            $_SESSION["user"] = $username;
            echo "Login successful!";
        } else {
            echo "Incorrect security answer.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
}
?>
