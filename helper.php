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
?>