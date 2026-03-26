<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "marketplace";

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check if the connection was successful
if (!$conn) {
    // If it fails, stop the script and show the error
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set the character set to utf8 to handle special symbols or emojis
mysqli_set_charset($conn, "utf8");
?>