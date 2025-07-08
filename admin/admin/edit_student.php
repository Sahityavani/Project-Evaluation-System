<?php
include('../db.php');

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $rollno = isset($_POST['rollno']) ? $conn->real_escape_string($_POST['rollno']) : '';
    $section = isset($_POST['section']) ? $conn->real_escape_string($_POST['section']) : '';

    // Validate input data
    if (empty($id) || empty($name) || empty($rollno)) {
        header('Location: add-student.php?error=Please fill all fields');
        exit;
    }

    // Check if the new roll number already exists
    $checkRollnoSql = "SELECT id FROM students WHERE rollno = ? AND id != ?";
    $checkStmt = $conn->prepare($checkRollnoSql);
    $checkStmt->bind_param('si', $rollno, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Roll number already exists
        header('Location: add-student.php?error=Roll number already exists');
        exit;
    }

    // Prepare SQL query for updating student details
    $updateSql = "UPDATE students SET name = ?, rollno = ?, section = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('sssi', $name, $rollno, $section, $id);

    if ($updateStmt->execute()) {
        // Redirect with success message
        header('Location: add-student.php?success=Student updated successfully');
    } else {
        // Redirect with error message
        header('Location: add-student.php?error=Failed to update student');
    }
    $updateStmt->close();
    $checkStmt->close();
}

$conn->close();
?>
