<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rollno'])) {
    $rollno = $conn->real_escape_string($_POST['rollno']);

    // Fetch current batch_id of the student
    $sql = "SELECT batch_id FROM students WHERE rollno = '$rollno'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $batch_id = $row['batch_id'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update student to remove from batch
            $updateStudentSql = "UPDATE students SET batch_id = NULL WHERE rollno = '$rollno'";
            if ($conn->query($updateStudentSql) !== TRUE) {
                throw new Exception("Failed to update student batch_id");
            }

            // Decrease batch_members count
            if ($batch_id) {
                $updateBatchSql = "UPDATE batches SET batch_members = batch_members - 1 WHERE batch_id = '$batch_id'";
                if ($conn->query($updateBatchSql) !== TRUE) {
                    throw new Exception("Failed to update batch members count");
                }
            }

            // Commit transaction
            $conn->commit();
            echo 'success';
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo 'error: ' . $e->getMessage();
        }
    } else {
        echo 'error: Student not found';
    }

    $conn->close();
}
?>
