<?php
$host = "localhost"; // Database host (usually localhost)
$username = "root"; // Database username
$password = ""; // Database password (leave empty if using XAMPP/WAMP default)
$dbname = "telemedicine"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>