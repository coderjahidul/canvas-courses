$('#syncBtn').click(function() {
    // Show a loading status
    $('#status').html('<div class="alert alert-info">Syncing data. Please wait.... <i class="fa fa-spinner fa-spin"></i></div>');

    // Make an AJAX request
    $.ajax({
        url: 'sync-courses-data.php',
        method: 'GET',
        success: function(data) {
            console.log("Data: " + data);
            // Display the success message
            $('#status').html('<div class="alert alert-success">Data synced successfully</div>');
        },
        error: function(xhr, status, error) {
            // Display the error message
            $('#status').html('<div class="alert alert-danger">Error: ' + error + '</div>');
        }
    });
});

$(document).on('click', '#import-btn', function(e) {
    e.preventDefault(); // Prevent default anchor behavior

    // Get the course_id from the data attribute
    let courseId = $(this).data('course-id');

    console.log("courseId: " + courseId);

    // AJAX request
    $.ajax({
        url: 'import.php', // PHP script handling the request
        type: 'GET', // HTTP method
        data: { 
            course_id: courseId
        }, // Data to send
        
        success: function(response) {
            console.log(response); // Log the entire response for debugging
            // Print the success message
            $('#status').html('<div class="alert alert-success">' + "Assignments imported successfully" + '</div>');
        },
        error: function(xhr, status, error) {
            // Handle AJAX error
            alert('An error occurred: ' + error);
            console.error(xhr.responseText); // Debugging information
        }
    });
});


