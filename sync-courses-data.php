<?php
// include log helper
include_once 'helper.php';

// include database connection
include_once 'db-connect.php';

// Connect to the database
$conn = getDatabaseConnection();

// get_courses_data_in_api function
function get_courses_data_in_api($conn){
    // call get api key
    $canvas_api_key = get_api_key($conn) ?? '';

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://csusm.instructure.com/api/v1/courses',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$canvas_api_key,
        'Cookie: _csrf_token=A2ASLG1olQ2UnSAe3beRjtjDi3fdSbF5EW8%2Fb3UsXFMzT3NjJj%2B6fMPeC3q18vTju5PuGJh4yT8pPkgrMBk4Jg%3D%3D; _legacy_normandy_session=wdFGKDXtU4L7XerGqnpgrg.lIgyJv1JIlcD0ltv-j3SUfGPKq3PSNgy4kdyOVAlCItCd3ab_l0y-IS5GeK0S4rpCYAABRWvAFPByv4E-WI8btZfep_uFNMvqtKwkeRMXveNUsH0HdlszFNozLTiLpge.H17iApxqnERVAK1TSa7wLTvb3ew.Z0qxIQ; canvas_session=wdFGKDXtU4L7XerGqnpgrg.lIgyJv1JIlcD0ltv-j3SUfGPKq3PSNgy4kdyOVAlCItCd3ab_l0y-IS5GeK0S4rpCYAABRWvAFPByv4E-WI8btZfep_uFNMvqtKwkeRMXveNUsH0HdlszFNozLTiLpge.H17iApxqnERVAK1TSa7wLTvb3ew.Z0qxIQ; log_session_id=053919db1b3f48fb9cc05d38a1e7bcf4'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response;
}

function insert_courses_data_in_db($conn) {
    // get_courses_data_in_api function to fetch courses data
    $get_courses_data = get_courses_data_in_api($conn);
    
    // Decode the JSON response
    $courses_data = json_decode($get_courses_data);
    
    // Table name
    $table_name = 'can_courses';
    
    
    if(!$conn) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Truncate the table
    $truncate_sql = "TRUNCATE TABLE $table_name";
    if ($conn->query($truncate_sql) !== TRUE) {
        die("Error truncating table: " . $conn->error);
    }
    
    // Prepare the SQL query with placeholders
    $sql = "INSERT INTO $table_name (course_id, name, account_id, uuid, course_code) 
            VALUES (?, ?, ?, ?, ?)";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    // Loop through each course and insert it into the database
    foreach ($courses_data as $course) {
        // Extract data from the course object
        $course_id = $course->id;    // Assuming API provides these fields
        $course_name = $course->name;  // Use actual course name here
        $account_id = $course->account_id;          // Replace with the actual account_id
        $course_uuid = $course->uuid;      // Replace with actual course uuid
        $course_code = $course->course_code;      // Replace with actual course code
        
        // Bind parameters
        $stmt->bind_param("isiss", $course_id, $course_name, $account_id, $course_uuid, $course_code);
        
        // Execute the query
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error . "\n";
        }
    }
    
    // Close the statement
    $stmt->close();
    
    // Close the connection
    $conn->close();
}

insert_courses_data_in_db($conn);
