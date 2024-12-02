<?php
// Include necessary files
include_once 'db-connect.php';
include_once 'helper.php';
require 'vendor/autoload.php'; // Include PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Import the assignments using the provided function
    $assignmentsJson = import_assignments($course_id);

    // Ensure the response is valid JSON
    if ($assignmentsJson) {
        // Decode JSON response into an array
        $assignments = json_decode($assignmentsJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON response from API',
            ]);
            exit;
        }
        // put_program_logs("Assignments: " . json_encode($assignments));
        // Check if we received valid assignment data
        if (isset($assignments)) {
            // put_program_logs("Assignments: " . json_encode($assignments['assignments']));
            // Create a new spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Define column headers
            $headers = [
                'ID',
                'Name',
                'Description',
                'Due At',
                'Unlock At',
                'Lock At',
                'Points Possible',
                'Grading Type',
                'Assignment Group ID',
                'Grading Standard ID',
                'Created At',
                'Updated At',
                'Submission Types',
                'Peer Reviews',
                'Automatic Peer Reviews',
                'Position',
                'Grade Group Students Individually',
                'Anonymous Peer Reviews',
                'Group Category ID',
                'Allowed Attempts',
                'Submissions Download URL',
                'Visible To Everyone',
                'Locked For User',
                'Lock Explanation'
            ];
            $columnIndex = 1; // Start from column 1 (A)
            foreach ($headers as $header) {
                $sheet->setCellValue([$columnIndex, 1], $header); // First row for headers
                $columnIndex++;
            }

            // Populate rows with assignment data
            $rowNumber = 2; // Start from the second row
            foreach ($assignments as $assignment) {
                $sheet->setCellValue('A' . $rowNumber, $assignment['id'] ?? '');
                $sheet->setCellValue('B' . $rowNumber, $assignment['name'] ?? '');
                $sheet->setCellValue('C' . $rowNumber, strip_tags($assignment['description'] ?? ''));
                $sheet->setCellValue('D' . $rowNumber, $assignment['due_at'] ?? '');
                $sheet->setCellValue('E' . $rowNumber, $assignment['unlock_at'] ?? '');
                $sheet->setCellValue('F' . $rowNumber, $assignment['lock_at'] ?? '');
                $sheet->setCellValue('G' . $rowNumber, $assignment['points_possible'] ?? '');
                $sheet->setCellValue('H' . $rowNumber, $assignment['grading_type'] ?? '');
                $sheet->setCellValue('I' . $rowNumber, $assignment['assignment_group_id'] ?? '');
                $sheet->setCellValue('J' . $rowNumber, $assignment['grading_standard_id'] ?? '');
                $sheet->setCellValue('K' . $rowNumber, $assignment['created_at'] ?? '');
                $sheet->setCellValue('L' . $rowNumber, $assignment['updated_at'] ?? '');
                $sheet->setCellValue('M' . $rowNumber, implode(', ', $assignment['submission_types'] ?? []));
                $sheet->setCellValue('N' . $rowNumber, $assignment['peer_reviews'] ? 'Yes' : 'No');
                $sheet->setCellValue('O' . $rowNumber, $assignment['automatic_peer_reviews'] ? 'Yes' : 'No');
                $sheet->setCellValue('P' . $rowNumber, $assignment['position'] ?? '');
                $sheet->setCellValue('Q' . $rowNumber, $assignment['grade_group_students_individually'] ? 'Yes' : 'No');
                $sheet->setCellValue('R' . $rowNumber, $assignment['anonymous_peer_reviews'] ? 'Yes' : 'No');
                $sheet->setCellValue('S' . $rowNumber, $assignment['group_category_id'] ?? '');
                $sheet->setCellValue('T' . $rowNumber, $assignment['allowed_attempts'] ?? '');
                $sheet->setCellValue('U' . $rowNumber, $assignment['submissions_download_url'] ?? '');
                $sheet->setCellValue('V' . $rowNumber, $assignment['visible_to_everyone'] ? 'Yes' : 'No');
                $sheet->setCellValue('W' . $rowNumber, $assignment['locked_for_user'] ? 'Yes' : 'No');
                $sheet->setCellValue('X' . $rowNumber, strip_tags($assignment['lock_explanation'] ?? ''));
                $rowNumber++;
            }

            // Set headers for Excel file download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="assignments.xlsx"');
            header('Cache-Control: max-age=0');

            // Write the spreadsheet to the output (this will trigger the download)
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No assignments found for this course.',
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch assignments data',
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Course ID is missing',
    ]);
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
    // put_program_logs($response);
    return $response;
}
?>
