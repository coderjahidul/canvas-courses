<?php
// Database connection function
function getDatabaseConnection() {
    // Database configuration
    $host = 'localhost'; // Change this if your database is on another server
    $username = 'root'; // Replace with your database username
    $password = ''; // Replace with your database password
    $dbname = 'canvas-courses'; // Your database name

    // Create a connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Function to create the table
function createCanCoursesTable() {
    $conn = getDatabaseConnection();

    // SQL to create the can_courses table
    $sql1 = "CREATE TABLE IF NOT EXISTS can_courses (
        id INT NOT NULL AUTO_INCREMENT,
        course_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        account_id INT NOT NULL,
        uuid VARCHAR(255) NOT NULL,
        course_code VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )";

    // Execute the first query
    if ($conn->query($sql1) !== TRUE) {
        echo "Error creating can_courses table: " . $conn->error;
    }

    // SQL to create the canvas_api_settings table
    $sql2 = "CREATE TABLE IF NOT EXISTS canvas_api_settings (
        id INT NOT NULL AUTO_INCREMENT,
        api_key VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    )";

    // Execute the second query
    if ($conn->query($sql2) !== TRUE) {
        echo "Error creating canvas_api_settings table: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}

// Call the function to create the tables
createCanCoursesTable();


?>
