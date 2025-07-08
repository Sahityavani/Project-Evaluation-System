<?php
include('../db.php');

header('Content-Type: application/json');

// Get POST data
$student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
$marks = isset($_POST['marks']) ? (int)$_POST['marks'] : 0;
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';

if (!$student_id || $marks < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided']);
    exit();
}

// Update marks and remarks
$sql = "UPDATE supervisor_evaluations SET marks = ?, remarks = ? WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isi', $marks, $remarks, $student_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Marks updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>
