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

// Logout on refresh
if (isset($_SESSION["last_page_load"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_page_load"] = time();

// Auto logout after 5 minutes of inactivity
$inactive_time = 300; // 5 minutes
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $inactive_time)) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();

// Fetch allowed time settings from the database
$query = "SELECT start_time, end_time FROM admin_settings WHERE id = 1"; // Assuming there is one row for settings
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);

// Default time if no settings are found
$allowed_start_time = isset($settings['start_time']) ? $settings['start_time'] : 6; // Default to 6 AM
$allowed_end_time = isset($settings['end_time']) ? $settings['end_time'] : 22; // Default to 10 PM

// Store last login time
if (!isset($_SESSION["last_login"])) {
    $_SESSION["last_login"] = date("Y-m-d H:i:s");
}

// Cybersecurity tips
$cyber_tips = [
    "1️⃣ **Use Strong Passwords** - Always create unique and complex passwords for different accounts. Use a password manager if necessary.",
    "2️⃣ **Enable Multi-Factor Authentication (MFA)** - Adding an extra layer of security helps prevent unauthorized access.",
    "3️⃣ **Be Cautious of Phishing Attacks** - Avoid clicking on suspicious links or attachments in emails and messages.",
    "4️⃣ **Keep Software and Systems Updated** - Regularly update your operating system, applications, and security patches.",
    "5️⃣ **Secure Your Network and Devices** - Use a firewall, encrypt sensitive data, and avoid public Wi-Fi for critical transactions."
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script>
        function updateClock() {
            let now = new Date();
            document.getElementById("clock").innerHTML = now.toLocaleTimeString();
            setTimeout(updateClock, 1000);
        }

        function getDeviceInfo() {
            let userAgent = navigator.userAgent;
            let deviceInfo = "Unknown Device";

            if (/Windows/i.test(userAgent)) deviceInfo = "Windows PC";
            else if (/Mac/i.test(userAgent)) deviceInfo = "MacBook or iMac";
            else if (/Linux/i.test(userAgent)) deviceInfo = "Linux PC";
            else if (/Android/i.test(userAgent)) deviceInfo = "Android Device";
            else if (/iPhone|iPad/i.test(userAgent)) deviceInfo = "iOS Device";

            document.getElementById("deviceInfo").innerHTML = "📱 Device: " + deviceInfo;
        }

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }

        // Auto logout after 5 minutes of inactivity
        let inactivityTime = 5 * 60 * 1000; // 5 minutes in milliseconds
        let logoutTimer;

        function resetTimer() {
            clearTimeout(logoutTimer);
            logoutTimer = setTimeout(() => {
                window.location.href = 'logout.php';
            }, inactivityTime);
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                document.getElementById("location").innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;

            // Display current latitude and longitude
            document.getElementById("latitude").innerHTML = "Latitude: " + latitude;
            document.getElementById("longitude").innerHTML = "Longitude: " + longitude;

            // Check if within allowed location (Kenya's latitude/longitude)
            if (latitude >= -5.0 && latitude <= 5.0 && longitude >= 34.0 && longitude <= 42.0) {
                document.getElementById("place").innerHTML = "Your location is within Kenya.";
            } else {
                document.getElementById("place").innerHTML = "You are outside the allowed location (Kenya).";
            }
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById("location").innerHTML = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById("location").innerHTML = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    document.getElementById("location").innerHTML = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById("location").innerHTML = "An unknown error occurred.";
                    break;
            }
        }

        window.onload = function () {
            updateClock();
            getDeviceInfo();
            resetTimer();
            document.body.addEventListener("mousemove", resetTimer);
            document.body.addEventListener("keydown", resetTimer);
            getLocation();
        };
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: green;
            color: #333;
            padding: 20px;
        }
        .dark-mode {
            background-color: #222;
            color: #fff;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .profile {
            width: 80px;
            height: 80px;
            background-color: #ccc;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 10px;
        }
        .profile img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }
        .cyber-tips {
            text-align: left;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 8px;
            margin-top: 10px;
        }
        button {
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        .admin-login {
            margin-top: 30px;
            padding: 15px;
            background: #ffeded;
            border-radius: 8px;
            border: 1px solid #ff6b6b;
        }
        .admin-login h3 {
            color: #d9534f;
        }
        .admin-login input {
            width: 80%;
            padding: 8px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        


    </style>
</head>
<body>
    

    <div class="container">
        <div class="profile">
            <img src="profile-icon.png">
        </div>

        <h2>🎉 Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>📅 Last login: <?php echo $_SESSION["last_login"]; ?></p>

        <div id="clock">🕒 Loading time...</div>
        <div id="deviceInfo">📱 Detecting device...</div>

        <h3>⏳ Allowed Time: <?php echo "$allowed_start_time:00 - $allowed_end_time:00"; ?></h3>
        <h3>🌍 Allowed Location: Kenya</h3>

        <h3>🌍 Current Location:</h3>
        <div id="location"></div>
        <div id="latitude"></div>
        <div id="longitude"></div>
        <div id="place"></div>

        <h3>🔒 Cybersecurity Tips:</h3>
        <div class="cyber-tips">
            <ul>
                <?php foreach ($cyber_tips as $tip) {
                    echo "<li>$tip</li>";
                } ?>
            </ul>
        </div>

        <button onclick="toggleDarkMode()">🌙 Toggle Dark Mode</button>
        <br>
        <a href="logout.php"><button>🚪 Logout</button></a>

    </div>
    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> Secure Dashboard. Contact: support@user.com</p>
    </div>

</body>
</html>
