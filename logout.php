<?php
require "db.php";
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="success">✅ You have successfully logged out.</h2>
        <a href="login.php" class="btn">Login Again</a>
    </div>
</body>
</html>
