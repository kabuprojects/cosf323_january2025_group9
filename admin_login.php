<?php
session_start();
include 'db.php';

$error = ""; // To store error messages


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Sanitize the inputs to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        // Fetch admin from the database
        $sql = "SELECT * FROM admins WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            
            if ($password === $row['password']) {
                $_SESSION['admin_id'] = $row['id']; 
                $_SESSION['admin_username'] = $row['username']; 

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "❌ Invalid password!";
            }
        } else {
            $error = "❌ Admin not found!";
        }
    } else {
        $error = "❌ Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: green;
            text-align: center;
        }
        form {
            background: #fff;
            width: 90%;
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            color: #800000;
            display: block;
            margin: 10px 0 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #800000;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background: #FFD700;
            color: #800000;
        }
    </style>
</head>
<body>
    <h2>🔐 Admin Login</h2>
    
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="post">
        <label>Username:</label> 
        <input type="text" name="username" required><br>
        
        <label>Password:</label> 
        <input type="password" name="password" required><br>
        
        <button type="submit">Login</button>
    </form>
    <a href="login.php"><h2>🔐 User Login</h2></a>
</body>
</html>

