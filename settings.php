<?php include_once("header.php");?>

<?php
    // Database table name
    $table_name = 'canvas_api_settings';

    // Connect to the database
    $conn = getDatabaseConnection();
    if (!$conn) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        api_key VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    )";
    $conn->query($sql);

    // Get current API key
    $api_key = get_api_key($conn) ?? '';

    // Fetch current API key
    // $sql = "SELECT * FROM $table_name LIMIT 1";
    // $result = $conn->query($sql);
    // $row = $result->fetch_assoc();
    // $api_key = $row['api_key'] ?? '';

    // If form is submitted, update API key
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['api_key'])) {
        $api_key = $_POST['api_key'];

        // Prepare and execute the update statement
        $stmt = $conn->prepare("UPDATE $table_name SET api_key = ? WHERE id = 1");
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
                if($message){
                    echo "<div class='alert alert-success mt-3'>$message</div>";
                }
            ?>
        </div>
    </div>
</div>

<?php include_once("footer.php"); ?>
