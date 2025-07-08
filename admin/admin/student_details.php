<?php
include('../db.php');
include('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if rollno is provided
    if (!isset($_POST['rollno']) || empty($_POST['rollno'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Roll number is required.'
        ]);
        exit;
    }

    // Sanitize and fetch the roll number
    $rollno = $conn->real_escape_string($_POST['rollno']);

    // Fetch student details from the database
    $sql = "SELECT id, name, rollno, section FROM students WHERE rollno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $rollno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Student found
        $student = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'data' => $student
        ]);
    } else {
        // Student not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Student not found.'
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>
