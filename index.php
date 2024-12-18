
<?php include_once("header.php");?>
<?php

?>
    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mt-4">Courses and Assignments</h1>
                <p>Manage your course data seamlessly with our tool.</p>
                <!-- Course Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Course Name</th>
                                <th>Course ID</th>
                                <th>Actions</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody id="courseTableBody">
                            <!-- Dynamic rows will be inserted here -->
                             <?php
                             $all_curses = get_all_courses();
                             if($all_curses->num_rows > 0){
                                while($row = $all_curses->fetch_assoc()){
                                    echo "<tr>";
                                    echo "<td>{$row['id']}</td>";
                                    echo "<td>{$row['name']}</td>";
                                    echo "<td>{$row['course_id']}</td>";
                                    echo "<td>";
                                    ?>
                                    <a href="#" data-course-id="<?php echo $row['course_id']; ?>" class="btn btn-success btn-sm" id="export-btn">Export Assignments Excl Sheet</a>
                                    <a href="#" data-course-id="<?php echo $row['course_id']; ?>" class="btn btn-success btn-sm" id="export-btn-google-sheet">Export Assignments Google Sheet</a>
                                    <?php
                                    echo "</td>";
                                    echo "<td>" . date("Y-m-d H:i:s", strtotime($row['created_at'])) . "</td>";

                                    echo "</tr>";
                                }
                             } else {
                                echo "<tr><td colspan='4'>No courses found.</td></tr>";
                             }
                             ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php include_once("footer.php");?>