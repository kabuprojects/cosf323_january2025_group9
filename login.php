<?php
session_start();
require "db.php";

date_default_timezone_set("Africa/Nairobi");

$allowed_start_time = 6;
$allowed_end_time = 22;

// Allowed location (Kenya coordinates)
$allowed_latitude = [-4.6, 4.6];
$allowed_longitude = [33.5, 41.5];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    
    // Fetch user details from database
    $stmt = $conn->prepare("SELECT id, password_hash, security_question, security_answer_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password_hash, $security_question, $security_answer_hash);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $current_hour = date("G");

            // Check if login is within allowed time and location
            if (
                ($current_hour >= $allowed_start_time && $current_hour <= $allowed_end_time) &&
                ($latitude >= $allowed_latitude[0] && $latitude <= $allowed_latitude[1]) &&
                ($longitude >= $allowed_longitude[0] && $longitude <= $allowed_longitude[1])
            ) {
                $_SESSION["user"] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION["pending_user"] = $username;
                $_SESSION["security_question"] = $security_question;
                header("Location: security_check.php");
                exit();
            }
        } else {
            echo "<div class='container error'>❌ Invalid credentials.</div>";
        }
    } else {
        echo "<div class='container error'>❌ User not found.</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: green;
    color:#333;
    margin: 0;
    padding: 0;
    text-align: center;
}
</style>

</head>
<body>
    <div class="container">
        <h2>🔐 User Login</h2>
        <form method="POST" onsubmit="return getLocation()">
            <label>Username:</label>
            <input type="text" name="username" required><br>

            <label>Password:</label>
            <input type="password" name="password" required><br>

            <!-- Hidden fields for geolocation -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <button type="submit">Login</button>
        </form>
        <p>New user? <a href="register.php">Register here</a></p>
        <a href="admin_login.php"><h2>🔐 Admin Login</h2></a>
    </div>

    <!-- JavaScript to Fetch Geolocation -->
    <script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById("latitude").value = position.coords.latitude;
                    document.getElementById("longitude").value = position.coords.longitude;
                    document.forms[0].submit();
                },
                function(error) {
                    alert("⚠️ Location access denied. Please enable GPS to continue.");
                }
            );
            return false; // Prevent default form submission
        } else {
            alert("⚠️ Geolocation is not supported by this browser.");
            return false;
        }
    }
    </script>
</body>
</html>
