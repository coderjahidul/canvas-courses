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
                'Post To SIS',
                'Moderated Grading',
                'Omit From Final Grade',
                'Intra Group Peer Reviews',
                'Anonymous Instructor Annotations',
                'Anonymous Grading',
                'Graders Anonymous To Graders',
                'Grader Count',
                'Grader Comments Visible To Graders',
                'Final Grader ID',
                'Grader Names Visible To Final Grader',
                'Allowed Attempts',
                'Annotatable Attachment ID',
                'Hide In Gradebook',
                'Lock Info - Lock At',
                'Lock Info - Can View',
                'Lock Info - Asset String',
                'Secure Params',
                'LTI Context ID',
                'Course ID',
                'Workflow State',
                'Important Dates',
                'Muted',
                'HTML URL',
                'Published',
                'Only Visible To Overrides',
                'Visible To Everyone',
                'Locked For User',
                'Lock Explanation',
                'Submissions Download URL',
                'Post Manually',
                'Anonymize Students',
                'Require Lockdown Browser',
                'Restrict Quantitative Data',
                'Original Course ID',
                'Original Assignment ID',
                'Original LTI Resource Link ID',
                'Original Assignment Name',
                'Original Quiz ID',
                'Graded Submissions Exist',
                'Is Quiz Assignment',
                'Can Duplicate',
                'Max Name Length',
                'Due Date Required',
                'In Closed Grading Period'
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
                $sheet->setCellValue('T' . $rowNumber, $assignment['post_to_sis'] ? 'Yes' : 'No');
                $sheet->setCellValue('U' . $rowNumber, $assignment['moderated_grading'] ? 'Yes' : 'No');
                $sheet->setCellValue('V' . $rowNumber, $assignment['omit_from_final_grade'] ? 'Yes' : 'No');
                $sheet->setCellValue('W' . $rowNumber, $assignment['intra_group_peer_reviews'] ? 'Yes' : 'No');
                $sheet->setCellValue('X' . $rowNumber, $assignment['anonymous_instructor_annotations'] ? 'Yes' : 'No');
                $sheet->setCellValue('Y' . $rowNumber, $assignment['anonymous_grading'] ? 'Yes' : 'No');
                $sheet->setCellValue('Z' . $rowNumber, $assignment['graders_anonymous_to_graders'] ? 'Yes' : 'No');
                $sheet->setCellValue('AA' . $rowNumber, $assignment['grader_count'] ?? '');
                $sheet->setCellValue('AB' . $rowNumber, $assignment['grader_comments_visible_to_graders'] ? 'Yes' : 'No');
                $sheet->setCellValue('AC' . $rowNumber, $assignment['final_grader_id'] ?? '');
                $sheet->setCellValue('AD' . $rowNumber, $assignment['grader_names_visible_to_final_grader'] ? 'Yes' : 'No');
                $sheet->setCellValue('AE' . $rowNumber, $assignment['allowed_attempts'] ?? '');
                $sheet->setCellValue('AF' . $rowNumber, $assignment['annotatable_attachment_id'] ?? '');
                $sheet->setCellValue('AG' . $rowNumber, $assignment['hide_in_gradebook'] ? 'Yes' : 'No');
            
                // Handle lock_info nested array
                $lockInfo = $assignment['lock_info'] ?? [];
                $sheet->setCellValue('AH' . $rowNumber, $lockInfo['lock_at'] ?? '');
                $sheet->setCellValue('AI' . $rowNumber, $lockInfo['can_view'] ? 'Yes' : 'No');
                $sheet->setCellValue('AJ' . $rowNumber, $lockInfo['asset_string'] ?? '');
            
                $sheet->setCellValue('AK' . $rowNumber, $assignment['secure_params'] ?? '');
                $sheet->setCellValue('AL' . $rowNumber, $assignment['lti_context_id'] ?? '');
                $sheet->setCellValue('AM' . $rowNumber, $assignment['course_id'] ?? '');
                $sheet->setCellValue('AN' . $rowNumber, $assignment['workflow_state'] ?? '');
                $sheet->setCellValue('AO' . $rowNumber, $assignment['important_dates'] ? 'Yes' : 'No');
                $sheet->setCellValue('AP' . $rowNumber, $assignment['muted'] ? 'Yes' : 'No');
                $sheet->setCellValue('AQ' . $rowNumber, $assignment['html_url'] ?? '');
                $sheet->setCellValue('AR' . $rowNumber, $assignment['published'] ? 'Yes' : 'No');
                $sheet->setCellValue('AS' . $rowNumber, $assignment['only_visible_to_overrides'] ? 'Yes' : 'No');
                $sheet->setCellValue('AT' . $rowNumber, $assignment['visible_to_everyone'] ? 'Yes' : 'No');
                $sheet->setCellValue('AU' . $rowNumber, $assignment['locked_for_user'] ? 'Yes' : 'No');
                $sheet->setCellValue('AV' . $rowNumber, strip_tags($assignment['lock_explanation'] ?? ''));
                $sheet->setCellValue('AW' . $rowNumber, $assignment['submissions_download_url'] ?? '');
                $sheet->setCellValue('AX' . $rowNumber, $assignment['post_manually'] ? 'Yes' : 'No');
                $sheet->setCellValue('AY' . $rowNumber, $assignment['anonymize_students'] ? 'Yes' : 'No');
                $sheet->setCellValue('AZ' . $rowNumber, $assignment['require_lockdown_browser'] ? 'Yes' : 'No');
                $sheet->setCellValue('BA' . $rowNumber, $assignment['restrict_quantitative_data'] ? 'Yes' : 'No');
                $sheet->setCellValue('BB' . $rowNumber, $assignment['original_course_id'] ?? '');
                $sheet->setCellValue('BC' . $rowNumber, $assignment['original_assignment_id'] ?? '');
                $sheet->setCellValue('BD' . $rowNumber, $assignment['original_lti_resource_link_id'] ?? '');
                $sheet->setCellValue('BE' . $rowNumber, $assignment['original_assignment_name'] ?? '');
                $sheet->setCellValue('BF' . $rowNumber, $assignment['original_quiz_id'] ?? '');
                $sheet->setCellValue('BG' . $rowNumber, $assignment['graded_submissions_exist'] ? 'Yes' : 'No');
                $sheet->setCellValue('BH' . $rowNumber, $assignment['is_quiz_assignment'] ? 'Yes' : 'No');
                $sheet->setCellValue('BI' . $rowNumber, $assignment['can_duplicate'] ? 'Yes' : 'No');
                $sheet->setCellValue('BJ' . $rowNumber, $assignment['max_name_length'] ?? '');
                $sheet->setCellValue('BK' . $rowNumber, $assignment['due_date_required'] ? 'Yes' : 'No');
                $sheet->setCellValue('BL' . $rowNumber, $assignment['in_closed_grading_period'] ? 'Yes' : 'No');
            
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
