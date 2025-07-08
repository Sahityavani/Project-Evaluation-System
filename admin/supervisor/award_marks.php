<?php
include('auth.php');
include('../db.php');
header('Content-Type: application/json');

// Get POST data
$batch_id = isset($_POST['batch']) ? $_POST['batch'] : 1;
$evaluation_type = isset($_POST['evaluation_type']) ? $_POST['evaluation_type'] : 1;
$sub_parts = isset($_POST['sub_parts']) ? $_POST['sub_parts'] : [];
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : [];

// Get sub_parts from POST
$evaluation_date = date('Y-m-d'); // Current date

if (!$batch_id || !$evaluation_type || empty($sub_parts)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided']);
    exit();
}

// Get supervisor ID
$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT id FROM supervisors WHERE username = '$username'";
$result = $conn->query($sql);
$user_row = $result->fetch_assoc();
$supervisor_id = $user_row['id'];

// Check for existing evaluations
$existing_evaluations = [];
$sql = "SELECT student_id FROM supervisor_evaluations
        WHERE supervisor_id = $supervisor_id AND batch_id = $batch_id AND evaluation_type = $evaluation_type";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $existing_evaluations[] = $row['student_id'];
}

// Get the maximum supervisor_evaluation_id
$sql = "SELECT MAX(supervisor_evaluation_id) as max_id FROM supervisor_evaluations";
$result = $conn->query($sql);
$max_id_row = $result->fetch_assoc();
$supervisor_evaluation_id = $max_id_row['max_id'] + 1;

foreach ($sub_parts as $student_id => $parts) {
    if (in_array($student_id, $existing_evaluations)) {
        header('Location: add-marks.php?error=Some students have already been evaluated for this type.');
        exit();
    }

    // Fetch student roll number
    $sql = "SELECT rollno FROM students WHERE id = $student_id";
    $result = $conn->query($sql);
    $student_row = $result->fetch_assoc();
    $student_rollno = $student_row['rollno'];
    error_log("Student Roll Number: " . $student_rollno); // Debug log

    // Handle remarks
    $student_remarks = isset($remarks[$student_id]) ? $remarks[$student_id] : '';
    error_log("Student Remarks: " . $student_remarks); // Debug log

    // Calculate total marks from sub_parts
    $total_marks = array_sum($parts);

    // Convert sub_parts to JSON format
    $sub_parts_json = json_encode($parts);

    // Construct and execute the insert query
    $insert_sql = "INSERT INTO supervisor_evaluations 
                  (supervisor_evaluation_id, batch_id, supervisor_id, student_id, marks, remarks, evaluation_date, student_rollno, evaluation_type, sub_parts)
                  VALUES 
                  ($supervisor_evaluation_id, $batch_id, $supervisor_id, $student_id, $total_marks, '$student_remarks', '$evaluation_date', '$student_rollno', $evaluation_type, '$sub_parts_json')";

    if (!$conn->query($insert_sql)) {
        error_log('Insert failed: ' . $conn->error);
    }
}

// Close connection
$conn->close();

// Redirect to the success page
header('Location: add-marks.php?success=Marks have been successfully awarded.');
exit();
?>