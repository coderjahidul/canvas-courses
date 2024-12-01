
<?php include_once("header.php");?>
<?php

?>
    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mt-4">Courses and Assignments</h1>
                <p>Manage your course data seamlessly with our tool.</p>

                <!-- Sync Data Button -->
                <div id="sync" class="mb-4">
                    <button class="btn btn-primary" id="syncBtn">Sync Courses Data</button>
                    <div id="status" class="mt-2"></div>
                </div>
                <?php
                ?>
                <!-- Course Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Course Name</th>
                                <th>Course ID</th>
                                <th>Actions</th>
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
                                    <a href="#" data-course-id="<?php echo $row['course_id']; ?>" data-name="<?php echo $row['name']; ?>" class="btn btn-success btn-sm" id="export-btn">Export Assignments</a>
                                    <?php
                                    echo "</td>";

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