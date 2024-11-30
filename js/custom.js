document.getElementById('syncBtn').addEventListener('click', function() {
    // Show a loading Status
    document.getElementById('status').innerHTML = '<div class="alert alert-info">Syncing data. Please wait.... <i class="fa fa-spinner fa-spin"></i></div>';

    // Make an ajax request
    fetch('sync-courses-data.php')
        .then(response => response.text())
        .then(data => {
            // Display the success message
            document.getElementById('status').innerHTML = `<div class="alert alert-success">Data synced successfully!</div>`;
        }).catch(error => {
            // Display the error message
            document.getElementById('status').innerHTML = '<div class="alert alert-danger">Error: ' + error + '</div>';
        });
});