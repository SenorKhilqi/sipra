<?php
// Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "sipra_db";

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");
?>