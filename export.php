<?php
    // Include necessary files
    include_once 'db-connect.php';
    include_once 'helper.php';
    // Include Composer's autoload file to load PhpSpreadsheet
    require 'vendor/autoload.php'; // Make sure this path is correct

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
   

    // Check if 'course_id' is set in the URL
    if (isset($_GET['course_id'])) {
        $course_id = $_GET['course_id'];

        // Call the import_assignments_sample function to get the data
        $assignments = import_assignments_sample();
        put_program_logs(print_r($assignments, true));
        if ($assignments) {
            // Create a new spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            foreach($assignments as $rowNumber => $row){
                foreach($row as $columnNumber => $value){
                    // Excel columns are A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z
                    $column = chr(65 + $columnNumber);
                    $sheet->setCellValue($column . ($rowNumber + 1), $value);
                }
            }

            // Set the Content-Type and Content-Disposition headers to prompt the download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $course_name . ' assignments.xlsx"');
            header('Cache-Control: max-age=0');

            // Create an Xlsx writer and output the file to the browser
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to import assignments',
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Course ID is missing',
        ]);
    }

    function import_assignments_sample() {
        // This function should import assignments based on the course ID
        // Return a sample array of assignments for demonstration (replace this with actual logic)
        return [
            ['Assignment Name', 'Due Date', 'Points'],
            ['Assignment 1', '2024-12-10', 100],
            ['Assignment 2', '2024-12-15', 80],
            ['Assignment 3', '2024-12-20', 90],
        ];
    }
    

    function import_assignments($course_id){
        // Connect to the database
        $conn = getDatabaseConnection();

        // get api key
        $canvas_api_key = get_api_key($conn) ?? '';

        // get all assignments
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://csusm.instructure.com/api/v1/courses/'.$course_id.'/assignments',
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
        put_program_logs($response);
        return $response;
    }
?>