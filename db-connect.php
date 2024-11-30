<?php
// Database connection function
function getDatabaseConnection() {
    // Database configuration
    $host = 'localhost'; // Change this if your database is on another server
    $username = 'root'; // Replace with your database username
    $password = 'password'; // Replace with your database password
    $dbname = 'canvas-courses'; // Your database name

    // Create a connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

?>
