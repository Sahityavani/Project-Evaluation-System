<?php
include('../db.php');

// Retrieve POST data
$evaluation_type = isset($_POST['evaluation_type']) ? (int)$_POST['evaluation_type'] : 0;
$marks = $_POST['marks'] ?? [];
$remarks = $_POST['remarks'] ?? [];

if (!$evaluation_type) {
    header('Location: edit-marks.php?error=Invalid evaluation type');
    exit();
}

// Update marks
foreach ($marks as $student_id => $mark) {
    $mark = (int)$mark;
    $remark = $conn->real_escape_string($remarks[$student_id] ?? '');

    $sql = "
        UPDATE supervisor_evaluations
        SET marks = ?, remarks = ?
        WHERE student_id = ? AND evaluation_type = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isii', $mark, $remark, $student_id, $evaluation_type);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the edit marks page with a success message
header('Location: edit-marks.php?success=Marks updated successfully');
exit();

$conn->close();
?>
