<?php
include('../db.php');

$batchId = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;

// Fetch students and their marks
$studentsQuery = "
    SELECT s.rollno, s.name AS student_name,
           e.evaluator_name, e.marks, e.remarks,
           (SELECT AVG(marks) FROM evaluations WHERE batch_id = $batchId AND evaluator_name != s.supervisor) AS average_marks,
           s.supervisor
    FROM students s
    LEFT JOIN evaluations e ON s.rollno = e.student_rollno
    WHERE s.batch_id = $batchId
";
$studentsResult = $conn->query($studentsQuery);

// Fetch supervisor marks
$supervisorMarksQuery = "
    SELECT s.supervisor, AVG(e.marks) AS avg_evaluator_marks, MAX(e.marks) AS supervisor_marks
    FROM students s
    LEFT JOIN evaluations e ON s.supervisor = e.evaluator_name
    WHERE s.batch_id = $batchId
    GROUP BY s.supervisor
";
$supervisorMarksResult = $conn->query($supervisorMarksQuery);
$supervisorMarks = $supervisorMarksResult->fetch_assoc();

// Output the HTML for the marks table
$output = '<table class="table table-bordered">';
$output .= '<thead><tr><th>Student Name</th><th>Evaluator Name</th><th>Marks</th><th>Remarks</th><th>Average Marks</th><th>Supervisor Marks</th><th>Total Marks</th></tr></thead>';
$output .= '<tbody>';

while ($student = $studentsResult->fetch_assoc()) {
    $avgMarks = $student['average_marks'] ? number_format($student['average_marks'], 2) : 'N/A';
    $supervisorMarks = $supervisorMarks['supervisor_marks'] ? number_format($supervisorMarks['supervisor_marks'], 2) : 'N/A';
    $totalMarks = ($student['average_marks'] ? $student['average_marks'] : 0) + ($supervisorMarks ? $supervisorMarks : 0);

    $output .= '<tr>';
    $output .= '<td>' . htmlspecialchars($student['student_name']) . '</td>';
    $output .= '<td>' . htmlspecialchars($student['evaluator_name']) . '</td>';
    $output .= '<td>' . htmlspecialchars($student['marks']) . '</td>';
    $output .= '<td>' . htmlspecialchars($student['remarks']) . '</td>';
    $output .= '<td>' . $avgMarks . '</td>';
    $output .= '<td>' . $supervisorMarks . '</td>';
    $output .= '<td>' . number_format($totalMarks, 2) . '</td>';
    $output .= '</tr>';
}

$output .= '</tbody>';
$output .= '</table>';

// Display roll number with total marks
$output .= '<h4 class="mt-4">Roll Number Summary</h4>';
$output .= '<table class="table table-bordered">';
$output .= '<thead><tr><th>Roll Number</th><th>Total Marks</th><th>Evaluators</th></tr></thead>';
$output .= '<tbody>';

$summaryQuery = "
    SELECT s.rollno, SUM(e.marks) AS total_marks,
           GROUP_CONCAT(DISTINCT e.evaluator_name) AS evaluators
    FROM students s
    LEFT JOIN evaluations e ON s.rollno = e.student_rollno
    WHERE s.batch_id = $batchId
    GROUP BY s.rollno
";
$summaryResult = $conn->query($summaryQuery);

while ($summary = $summaryResult->fetch_assoc()) {
    $output .= '<tr>';
    $output .= '<td>' . htmlspecialchars($summary['rollno']) . '</td>';
    $output .= '<td>' . number_format($summary['total_marks'], 2) . '</td>';
    $output .= '<td>' . htmlspecialchars($summary['evaluators']) . '</td>';
    $output .= '</tr>';
}

$output .= '</tbody>';
$output .= '</table>';

echo $output;
?>
