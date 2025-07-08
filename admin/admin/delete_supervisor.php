<?php
include('../db.php');

// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if headers are already sent
if (headers_sent()) {
    echo json_encode(['error' => 'Headers already sent.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $id = $conn->real_escape_string($_POST['id']);

    // Retrieve the supervisor's username for updating batches and students
    $usernameQuery = "SELECT username FROM supervisors WHERE id = '$id'";
    $usernameResult = $conn->query($usernameQuery);

    if ($usernameResult->num_rows === 0) {
        echo json_encode(['error' => 'Supervisor not found.']);
        exit();
    }

    $usernameRow = $usernameResult->fetch_assoc();
    $username = $usernameRow['username'];

    // Update batches table to set supervisor to NULL
    $updateBatchSql = "UPDATE batches SET supervisor = NULL WHERE supervisor = '$username'";
    if ($conn->query($updateBatchSql) !== TRUE) {
        echo json_encode(['error' => 'Error updating batches table: ' . $conn->error]);
        exit();
    }

    // Update students table to set supervisor to NULL
    $updateStudentsSql = "UPDATE students SET supervisor = NULL WHERE supervisor = '$username'";
    if ($conn->query($updateStudentsSql) !== TRUE) {
        echo json_encode(['error' => 'Error updating students table: ' . $conn->error]);
        exit();
    }

    // Prepare the DELETE statement
    $sql = "DELETE FROM supervisors WHERE id = '$id'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => 'Supervisor deleted successfully.']);
    } else {
        echo json_encode(['error' => 'Error deleting supervisor: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}

$conn->close();
?>
