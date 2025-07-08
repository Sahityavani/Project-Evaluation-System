<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['batch_id'])) {
    $batch_id = $conn->real_escape_string($_POST['batch_id']);

    // Fetch students in the batch
    $studentsQuery = "
        SELECT rollno, name, email, mobile
        FROM students
        WHERE batch_id = '$batch_id'
    ";
    $studentsResult = $conn->query($studentsQuery);

    if ($studentsResult->num_rows > 0) {
        echo '<div class="row">';
        while ($student = $studentsResult->fetch_assoc()) {
            echo '
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Roll No: ' . htmlspecialchars($student['rollno']) . '</h6>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">' . htmlspecialchars($student['name']) . '</h5>
                            <p class="text-muted mb-0">Email: ' . htmlspecialchars($student['email']) . '</p>
                            <p class="text-muted mb-0">Mobile: ' . htmlspecialchars($student['mobile']) . '</p>
                        </div>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-secondary edit-student-btn" 
                                    data-name="' . htmlspecialchars($student['name']) . '"
                                    data-rollno="' . htmlspecialchars($student['rollno']) . '"
                                    data-email="' . htmlspecialchars($student['email']) . '"
                                    data-mobile="' . htmlspecialchars($student['mobile']) . '">Edit</button>
                            <button type="button" class="btn btn-danger delete-student-btn" 
                                    data-rollno="' . htmlspecialchars($student['rollno']) . '">Delete</button>
                        </div>
                    </div>
                </div>
            ';
        }
        echo '</div>';
    } else {
        echo '<p>No students found for this batch.</p>';
    }

    $conn->close();
}
?>
