<?php
$servername = "localhost";
$username = "root"; 
$password = "leteipa";
$dbname = "auth_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use UTF-8 character encoding
$conn->set_charset("utf8");

?>
