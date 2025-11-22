<?php
$servername = "1";
$username = "1";  // Your phpMyAdmin username, usually "root"
$password = "1";      // Your phpMyAdmin password, usually blank for local setups
$dbname = "1"; // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
