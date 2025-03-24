<?php
session_start();
require "db.php";
date_default_timezone_set("Africa/Nairobi");

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION["user"];

// Auto logout after 5 minutes of inactivity
$inactive_time = 300; // 5 minutes
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $inactive_time)) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();

// Fetch allowed time and location settings from the database
$stmt = $conn->prepare("SELECT start_time, end_time, min_latitude, max_latitude, min_longitude, max_longitude FROM admin_settings WHERE id = ?");
$id = 1; // Default admin settings
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
$stmt->close();

// Default settings if no data is found
$allowed_start_time = isset($settings['start_time']) ? (int)$settings['start_time'] : 6;
$allowed_end_time = isset($settings['end_time']) ? (int)$settings['end_time'] : 22;
$min_latitude = isset($settings['min_latitude']) ? $settings['min_latitude'] : -5.0;
$max_latitude = isset($settings['max_latitude']) ? $settings['max_latitude'] : 5.0;
$min_longitude = isset($settings['min_longitude']) ? $settings['min_longitude'] : 34.0;
$max_longitude = isset($settings['max_longitude']) ? $settings['max_longitude'] : 42.0;

// Get user's current location
$current_latitude = -1.286389;
$current_longitude = 36.817223;
$is_within_location = ($current_latitude >= $min_latitude && $current_latitude <= $max_latitude) &&
    ($current_longitude >= $min_longitude && $current_longitude <= $max_longitude);

// Check if the current time is within the allowed time range
$current_hour = (int)date("H");
$is_within_time = $current_hour >= $allowed_start_time && $current_hour < $allowed_end_time;

// Store last login time
if (!isset($_SESSION["last_login"])) {
    $_SESSION["last_login"] = date("Y-m-d H:i:s");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; display: flex; flex-direction: column; min-height: 100vh; }
        .sidebar {
            width: 250px;
            background: #f4f4f4;
            padding: 20px;
            border-right: 1px solid #ccc;
            height: 100vh;
            position: fixed;
            transition: all 0.3s ease;
            overflow-y: auto;
            left: 0;
        }
        .sidebar.closed { width: 0; padding: 0; overflow: hidden; }
        .sidebar a { display: block; padding: 10px; border-radius: 5px; text-decoration: none; color: #333; transition: background 0.3s ease; }
        .sidebar a:hover { background-color: #007bff; color: white; }
        .sidebar a.logout { color: red; font-weight: bold; }
        .toggle-btn {
            background: #007bff;
            color: #fff;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s ease;
            position: fixed;
            left: 260px;
            top: 10px;
        }
        .toggle-btn:hover { background: #0056b3; }
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .content.full-width { margin-left: 0; }
        .dashboard-card {
            background: white;
            padding: 90px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 50%;
        }
        h2, h3, p { margin-bottom: 15px; }
        .footer { background: #333; color: #fff; text-align: center; padding: 10px; margin-top: auto; }
        .footer a { color: #007bff; text-decoration: none; margin: 0 10px; }
        .footer a:hover { text-decoration: underline; }
    </style>
    <script>
        function toggleSidebar() {
            var sidebar = document.querySelector(".sidebar");
            var content = document.querySelector(".content");
            sidebar.classList.toggle("closed");
            content.classList.toggle("full-width");
        }
        function updateTime() {
            document.getElementById("current-time").innerText = new Date().toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
    </script>
</head>
<body>
    <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
    <div class="sidebar">
        <a href="dashboard.php">Personal Profile</a>
        <a href="tips.php">Cybersecurity Tips</a>
        <a href="help.php">Help</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>

    <div class="content">
        <div class="dashboard-card">
            <h2>🎉 Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>📅 Last login: <?php echo $_SESSION["last_login"]; ?></p>
            <p>🕒 Current Time: <span id="current-time"><?php echo date("H:i:s"); ?></span></p>
            <p>📍 Current location: Latitude <?php echo $current_latitude; ?>, Longitude <?php echo $current_longitude; ?></p>
            <h3>⏳ <h3>⏳ Allowed Time: <?php echo date("h:i A", strtotime("$allowed_start_time:00")) . " - " . date("h:i A", strtotime("$allowed_end_time:00")); ?></h3>
</h3>
            <h3>📌 Allowed Location: Latitude <?php echo "$min_latitude to $max_latitude"; ?>, Longitude <?php echo "$min_longitude to $max_longitude"; ?></h3>
            <p><?php echo $is_within_time ? "✅ You are within the allowed time range." : "❌ You are outside the allowed time range."; ?></p>
            <p><?php echo $is_within_location ? "✅ You are within the allowed location range." : "❌ You are outside the allowed location range."; ?></p>
        </div>
        <footer class="footer">
        <p>🌐 <a href="#">Facebook</a> | <a href="#">Twitter</a> | <a href="#">LinkedIn</a></p>
        <p>&copy; <?php echo date("Y"); ?> AUTSYSTEMS LTD. All rights reserved.</p>
    </footer>
    </div>
</body>
</html>
