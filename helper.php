<?php 
// canvas api key function
function get_api_key($conn){
    $table_name = 'canvas_api_settings';
    $sql = "SELECT * FROM $table_name LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $api_key = $row['api_key'] ?? '';
    return $api_key;
}

// get all courses
function get_all_courses(){
    $conn = getDatabaseConnection();
    $sql = "SELECT * FROM can_courses";
    $result = $conn->query($sql);
    return $result;
}

// Function to log program data
function put_program_logs($data) {
    // Ensure the directory for logs exists
    $directory = __DIR__ . '/program_logs/';
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    // Construct the log file path
    $file_name = $directory . 'program_logs.log';

    // Append the current datetime to the log entry
    $current_datetime = date('Y-m-d H:i:s');
    $formatted_data = "API Response:\n" . $data . "\nLogged At: " . $current_datetime . "\n\n";

    // Write the log entry to the file
    if (file_put_contents($file_name, $formatted_data, FILE_APPEND | LOCK_EX) !== false) {
        echo "Data appended to file successfully.\n";
    } else {
        echo "Failed to append data to file.\n";
    }
}
?>