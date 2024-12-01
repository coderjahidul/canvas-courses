<?php include_once("header.php");?>

<?php
    // Database table name
    $table_name = 'canvas_api_settings';

    // Connect to the database
    $conn = getDatabaseConnection();
    if (!$conn) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get current API key
    $api_key = get_api_key($conn) ?? '';

    // Update API key
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['api_key'])) {
        $api_key = isset($_POST['api_key']) ? $_POST['api_key'] : '';
    
        // Prepare and execute the insert or update statement
        $stmt = $conn->prepare("
            INSERT INTO $table_name (id, api_key) 
            VALUES (1, ?) 
            ON DUPLICATE KEY UPDATE api_key = VALUES(api_key)
        ");
        $stmt->bind_param("s", $api_key); // "s" is for string type
    
        if ($stmt->execute()) {
            $message = "API key updated successfully!";
        } else {
            $message = "Error updating API key: " . $stmt->error;
        }
    }
    
?>


<!-- Main Content -->
<div class="container">
    <div class="row">
        <div class="col-6">
            <h1 class="mt-4">Courses API Settings Page</h1>
            
            <!-- API key form -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="mb-3">
                    <label for="api_key" class="form-label">Canvas API Key</label>
                    <input type="password" name="api_key" id="api_key" placeholder="Enter Canvas API Key" class="form-control" value="<?php echo htmlspecialchars($api_key); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
            <?php 
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['api_key'])){
                    echo "<div class='alert alert-success mt-3'>$message</div>";
                }
            ?>
        </div>
    </div>
</div>

<?php include_once("footer.php"); ?>
