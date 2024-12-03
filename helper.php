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

// Function to import assignments
function import_assignments($course_id){
    // Connect to the database
    $conn = getDatabaseConnection();

    // get api key
    $canvas_api_key = get_api_key($conn) ?? '';

    // get all assignments
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://csusm.beta.instructure.com/api/v1/courses/'.$course_id.'/assignments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$canvas_api_key,
        'Cookie: _csrf_token=m%2BCBDStH84cEdl8amUKaUNR4UJ9dxizdLMxeBy2%2B5mbOmLZORyq643ZZK1zgNNkeoQsF6xOOb5RH4xFxafaFCA%3D%3D; _legacy_normandy_session=uSc2QH_is95QnXMYqpf30w.q88iV1xRDOLjodXfYNwPvsiTNcD5STCRNKtfGcdOm1SVe3Vt6Wk3mHyT1S0It8n4YqeeZjHbrOUfZ5T1s6SPbh8dkjeDbNb6jyIVBTB-gYwGEDlosiuBfKUAqqi5ooWI.ZjB3twIkbZa0DMSPmfZd44Fl6Bg.Z0vaew; canvas_session=uSc2QH_is95QnXMYqpf30w.q88iV1xRDOLjodXfYNwPvsiTNcD5STCRNKtfGcdOm1SVe3Vt6Wk3mHyT1S0It8n4YqeeZjHbrOUfZ5T1s6SPbh8dkjeDbNb6jyIVBTB-gYwGEDlosiuBfKUAqqi5ooWI.ZjB3twIkbZa0DMSPmfZd44Fl6Bg.Z0vaew; log_session_id=7a6be3c25efcec87162760894c2c1576'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // put_program_logs($response);
    return $response;
}


?>