<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    // Redirect to login if the admin is not logged in
    header("Location: admin_login.php");
    exit();
}

// Initialize error message
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetching data from form
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $min_latitude = $_POST['min_latitude'];
    $max_latitude = $_POST['max_latitude'];
    $min_longitude = $_POST['min_longitude'];
    $max_longitude = $_POST['max_longitude'];

    // Prepare the query for insertion or update
    $sql = "INSERT INTO admin_settings (id, start_time, end_time, min_latitude, max_latitude, min_longitude, max_longitude) 
            VALUES (1, ?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            start_time = VALUES(start_time), end_time = VALUES(end_time), 
            min_latitude = VALUES(min_latitude), max_latitude = VALUES(max_latitude), 
            min_longitude = VALUES(min_longitude), max_longitude = VALUES(max_longitude)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssdddd", $start_time, $end_time, $min_latitude, $max_latitude, $min_longitude, $max_longitude);

    // Execute the statement
    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success_message'] = "Settings updated successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // If an error occurs
        $error = "❌ Error updating settings!";
    }
}

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';

if ($success_message) {
    unset($_SESSION['success_message']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: green;
            text-align: center;
        }
        form {
            background: #fff;
            width: 90%;
            max-width: 600px;
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
    <h2>🔧 Admin Settings</h2>

    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success_message)) echo "<p style='color: blue;'>$success_message</p>"; ?>

    <form method="post">
        <label>Start Time:</label>
        <input type="time" name="start_time" required><br>
        
        <label>End Time:</label>
        <input type="time" name="end_time" required><br>
        
        <label>Min Latitude:</label>
        <input type="number" step="any" name="min_latitude" required><br>
        
        <label>Max Latitude:</label>
        <input type="number" step="any" name="max_latitude" required><br>
        
        <label>Min Longitude:</label>
        <input type="number" step="any" name="min_longitude" required><br>
        
        <label>Max Longitude:</label>
        <input type="number" step="any" name="max_longitude" required><br>

        <button type="submit">Save Settings</button>
    </form>
</body>
</html>
