<?php
include('../db.php');
include('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evaluation_type = isset($_POST['evaluation_type']) ? (int)$_POST['evaluation_type'] : 0;
     $evaluator_id = isset($_POST['evaluator_id']) ? (int)$_POST['evaluator_id'] : 0;
    $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : 0;
    $sub_parts_marks = isset($_POST['marks']) ? $_POST['marks'] : [];
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : [];

    if (!$evaluation_type || !$batch_id || empty($sub_parts_marks) || empty($evaluator_id)) {
        header('Location: edit-marks.php?error=Invalid input');
        exit();
    }

    $updateSuccess = true;
    $evaluation_date = date('Y-m-d H:i:s'); // Current date and time for evaluation_date

    foreach ($sub_parts_marks as $student_id => $student_marks) {
        // Calculate total marks
        $totalMarks = array_sum($student_marks);

        // Prepare JSON data for sub-parts marks
        $sub_parts_json = json_encode($student_marks);

        // Get remarks for the student
        $remark = isset($remarks[$student_id]) ? $remarks[$student_id] : '';

        // Update marks, sub_parts, total_marks, remarks, and evaluation_date for each student
        $sql = "
            UPDATE mrg_evaluations
            SET sub_parts = ?, marks = ?, remarks = ?, evaluation_date = ?
            WHERE evaluation_type = ? AND batch_id = ? AND student_id = ? AND evaluator_id = ?
        ";
        $stmt = $conn->prepare($sql);

        // Bind parameters: s (string) for sub_parts_json, i (integer) for totalMarks, s (string) for remarks, s (string) for evaluation_date, i (integer) for evaluation_type, i (integer) for batch_id, i (integer) for student_id
        $stmt->bind_param('sissiiis', $sub_parts_json, $totalMarks, $remark, $evaluation_date, $evaluation_type, $batch_id, $student_id, $evaluator_id);

        if (!$stmt->execute()) {
            $updateSuccess = false;
            break;
        }
    }

    $stmt->close();
    $conn->close();

    if ($updateSuccess) {
        header('Location: edit-marks.php?success=Marks updated successfully');
    } else {
        header('Location: edit-marks.php?error=Failed to update marks');
    }
} else {
    header('Location: edit-marks.php?error=Invalid request');
}
exit();
?>
