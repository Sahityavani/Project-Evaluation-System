<?php
include('../db.php');

// Set the response content type to JSON
header('Content-Type: application/json');

// Initialize the response array
$response = [
    'status' => 'error',
    'message' => 'Invalid request'
];

// Check if 'rollno' is provided in the query string
if (isset($_GET['rollno']) && !empty($_GET['rollno'])) {
    $rollno = $conn->real_escape_string($_GET['rollno']);

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Check if the student exists and fetch their batch_id
        $checkSql = "SELECT id, batch_id FROM students WHERE rollno = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $rollno);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Fetch the student's data
            $student = $checkResult->fetch_assoc();
            $id = $student['id'];
            $batchId = $student['batch_id'];

            if (!empty($batchId)) {
                // Student is allotted to a batch, cannot delete
                $response['message'] = 'Student is allotted to a batch and cannot be deleted';
            } else {
                // Delete the student
                $deleteSql = "DELETE FROM students WHERE rollno = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param('s', $rollno);
                $deleteStmt->execute();

                if ($deleteStmt->affected_rows > 0) {
                    // Update rollno of subsequent students
                    $updateSql = "UPDATE students 
                                  SET rollno = CONCAT('B', LPAD(CAST(SUBSTRING(rollno, 2) AS UNSIGNED) - 1, 3, '0')) 
                                  WHERE SUBSTRING(rollno, 1, 1) = 'B' 
                                  AND CAST(SUBSTRING(rollno, 2) AS UNSIGNED) > (SELECT CAST(SUBSTRING(rollno, 2) AS UNSIGNED) FROM students WHERE id = ?)";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param('i', $id);
                    $updateStmt->execute();

                    // Commit transaction
                    $conn->commit();

                    // Set success response
                    $response['status'] = 'success';
                    $response['message'] = 'Student deleted successfully';
                } else {
                    // Rollback transaction in case of failure
                    $conn->rollback();
                    $response['message'] = 'Failed to delete student';
                }

                $deleteStmt->close();
                $updateStmt->close();
            }
        } else {
            // Student does not exist
            $response['message'] = 'Student not found';
        }

        $checkStmt->close();
    } catch (Exception $e) {
        // Rollback transaction on exception and log error
        $conn->rollback();
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
} else {
    // Invalid request
    $response['message'] = 'Invalid request';
}

$conn->close();

// Output the JSON response
echo json_encode($response);
?>
