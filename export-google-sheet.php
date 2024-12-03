<?php 
// Include necessary files
include_once 'db-connect.php';
include_once 'helper.php';
require 'vendor/autoload.php';

function insertDataIntoGoogleSheet($spreadsheetId, $assignments, $sheetName = "Sheet3") {
    $credentials = __DIR__ . "/google-sheet-secrets.json";

    $client = new \Google_Client();
    $client->setApplicationName('Canvas Course Exporter');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAuthConfig($credentials);
    $client->setAccessType('offline');

    $service = new \Google_Service_Sheets($client);

    // Check if the sheet exists and create it if not
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetExists = false;

    foreach ($spreadsheet->getSheets() as $sheet) {
        if ($sheet->getProperties()->getTitle() === $sheetName) {
            $sheetExists = true;
            break;
        }
    }

    if (!$sheetExists) {
        // Create a new sheet with the specified name
        $addSheetRequest = new \Google_Service_Sheets_Request([
            'addSheet' => [
                'properties' => ['title' => $sheetName]
            ]
        ]);

        $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [$addSheetRequest]
        ]);

        $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    }

    // Define headers for the sheet
    $headers = [
        'ID', 'Name', 'Description', 'Due At', 'Unlock At', 'Lock At', 'Points Possible',
        'Grading Type', 'Assignment Group ID', 'Grading Standard ID', 'Created At',
        'Updated At', 'Submission Types', 'Peer Reviews', 'Automatic Peer Reviews',
        'Position', 'Grade Group Students Individually', 'Anonymous Peer Reviews',
        'Group Category ID', 'Post To SIS', 'Moderated Grading', 'Omit From Final Grade',
        'Intra Group Peer Reviews', 'Anonymous Instructor Annotations', 'Anonymous Grading',
        'Graders Anonymous To Graders', 'Grader Count', 'Grader Comments Visible To Graders',
        'Final Grader ID', 'Grader Names Visible To Final Grader', 'Allowed Attempts',
        'Annotatable Attachment ID', 'Hide In Gradebook', 'Lock Info - Lock At',
        'Lock Info - Can View', 'Lock Info - Asset String', 'Secure Params', 'LTI Context ID',
        'Course ID', 'Workflow State', 'Important Dates', 'Muted', 'HTML URL', 'Published',
        'Only Visible To Overrides', 'Visible To Everyone', 'Locked For User',
        'Lock Explanation', 'Submissions Download URL', 'Post Manually', 'Anonymize Students',
        'Require Lockdown Browser', 'Restrict Quantitative Data', 'Original Course ID',
        'Original Assignment ID', 'Original LTI Resource Link ID', 'Original Assignment Name',
        'Original Quiz ID', 'Graded Submissions Exist', 'Is Quiz Assignment',
        'Can Duplicate', 'Max Name Length', 'Due Date Required', 'In Closed Grading Period'
    ];

    // Prepare data to insert
    $values = [];
    $values[] = $headers; // Add headers to the first row

    foreach ($assignments as $assignment) {
        $values[] = [
            $assignment['id'] ?? '',
            $assignment['name'] ?? '',
            strip_tags($assignment['description'] ?? ''),
            $assignment['due_at'] ?? '',
            $assignment['unlock_at'] ?? '',
            $assignment['lock_at'] ?? '',
            $assignment['points_possible'] ?? '',
            $assignment['grading_type'] ?? '',
            $assignment['assignment_group_id'] ?? '',
            $assignment['grading_standard_id'] ?? '',
            $assignment['created_at'] ?? '',
            $assignment['updated_at'] ?? '',
            implode(', ', $assignment['submission_types'] ?? []),
            $assignment['peer_reviews'] ? 'Yes' : 'No',
            $assignment['automatic_peer_reviews'] ? 'Yes' : 'No',
            $assignment['position'] ?? '',
            $assignment['grade_group_students_individually'] ? 'Yes' : 'No',
            $assignment['anonymous_peer_reviews'] ? 'Yes' : 'No',
            $assignment['group_category_id'] ?? '',
            $assignment['post_to_sis'] ? 'Yes' : 'No',
            $assignment['moderated_grading'] ? 'Yes' : 'No',
            $assignment['omit_from_final_grade'] ? 'Yes' : 'No',
            $assignment['intra_group_peer_reviews'] ? 'Yes' : 'No',
            $assignment['anonymous_instructor_annotations'] ? 'Yes' : 'No',
            $assignment['anonymous_grading'] ? 'Yes' : 'No',
            $assignment['graders_anonymous_to_graders'] ? 'Yes' : 'No',
            $assignment['grader_count'] ?? '',
            $assignment['grader_comments_visible_to_graders'] ? 'Yes' : 'No',
            $assignment['final_grader_id'] ?? '',
            $assignment['grader_names_visible_to_final_grader'] ? 'Yes' : 'No',
            $assignment['allowed_attempts'] ?? '',
            $assignment['annotatable_attachment_id'] ?? '',
            $assignment['hide_in_gradebook'] ? 'Yes' : 'No',
            $assignment['lock_info']['lock_at'] ?? '',
            $assignment['lock_info']['can_view'] ?? '',
            $assignment['lock_info']['asset_string'] ?? '',
            $assignment['secure_params'] ?? '',
            $assignment['lti_context_id'] ?? '',
            $assignment['course_id'] ?? '',
            $assignment['workflow_state'] ?? '',
            $assignment['important_dates'] ? 'Yes' : 'No',
            $assignment['muted'] ? 'Yes' : 'No',
            $assignment['html_url'] ?? '',
            $assignment['published'] ? 'Yes' : 'No',
            $assignment['only_visible_to_overrides'] ? 'Yes' : 'No',
            $assignment['visible_to_everyone'] ? 'Yes' : 'No',
            $assignment['locked_for_user'] ? 'Yes' : 'No',
            strip_tags($assignment['lock_explanation'] ?? ''),
            $assignment['submissions_download_url'] ?? '',
            $assignment['post_manually'] ? 'Yes' : 'No',
            $assignment['anonymize_students'] ? 'Yes' : 'No',
            $assignment['require_lockdown_browser'] ? 'Yes' : 'No',
            $assignment['restrict_quantitative_data'] ? 'Yes' : 'No',
            $assignment['original_course_id'] ?? '',
            $assignment['original_assignment_id'] ?? '',
            $assignment['original_lti_resource_link_id'] ?? '',
            $assignment['original_assignment_name'] ?? '',
            $assignment['original_quiz_id'] ?? '',
            $assignment['graded_submissions_exist'] ? 'Yes' : 'No',
            $assignment['is_quiz_assignment'] ? 'Yes' : 'No',
            $assignment['can_duplicate'] ? 'Yes' : 'No',
            $assignment['max_name_length'] ?? '',
            $assignment['due_date_required'] ? 'Yes' : 'No',
            $assignment['in_closed_grading_period'] ? 'Yes' : 'No'
        ];
    }

    // Write data to Google Sheets
    $body = new \Google_Service_Sheets_ValueRange(['values' => $values]);
    $params = ['valueInputOption' => 'RAW', 'insertDataOption' => 'INSERT_ROWS'];

    $response = $service->spreadsheets_values->append(
        $spreadsheetId,
        $sheetName . "!A1", // Add cell reference
        $body,
        $params
    );

    return "Successfully Inserted Assignments Data into Sheet: $sheetName";
}




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
            $spreadsheetId = "147-fRjXPWO4yjDD17BFMFiLKmkIPJSkoTHoT3WOOV6I";

            // insert data in google sheet
            $insert_data = insertDataIntoGoogleSheet($spreadsheetId, $assignments, $course_id);
            echo $insert_data;
            
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

?>