<?php
include('auth.php');
include('../db.php');

$batch_id = isset($_GET['batch_id']) ? $conn->real_escape_string($_GET['batch_id']) : '';
$evaluation_type = isset($_GET['evaluation_type']) ? $conn->real_escape_string($_GET['evaluation_type']) : '';

$type = "SELECT type FROM evaluation_type where id = $evaluation_type";
$type_check = $conn->query($type);
$row = $type_check->fetch_assoc();
$type = $row['type'];


// Fetch evaluation type names from the evaluation_type table
$evaluationTypeNames = [];
$evaluationTypeQuery = "SELECT id, name FROM evaluation_type";
$evaluationTypeResult = $conn->query($evaluationTypeQuery);
while ($row = $evaluationTypeResult->fetch_assoc()) {
    $evaluationTypeNames[$row['id']] = $row['name'];
}

$response = [];

if ($batch_id && $evaluation_type && $type == 'MRG') {
    // Fetch students in the batch
    $studentsQuery = "
        SELECT rollno, name
        FROM students
        WHERE batch_id = '$batch_id'
    ";
    $studentsResult = $conn->query($studentsQuery);
    $students = [];
    while ($row = $studentsResult->fetch_assoc()) {
        $students[] = $row;
    }

    // Fetch evaluations for each student
    foreach ($students as $student) {
        $studentEvaluations = [];

        // Fetch mrg_evaluations
        $mrgEvaluationsQuery = "
            SELECT ev.marks, ev.remarks, e.name AS evaluator_name, ev.sub_parts
            FROM mrg_evaluations ev
            LEFT JOIN evaluators e ON ev.evaluator_id = e.evaluator_id
            WHERE ev.batch_id = ? AND ev.evaluation_type = ? AND ev.student_rollno = ?
        ";
        $mrgEvaluationsStmt = $conn->prepare($mrgEvaluationsQuery);
        $mrgEvaluationsStmt->bind_param('iis', $batch_id, $evaluation_type, $student['rollno']);
        $mrgEvaluationsStmt->execute();
        $mrgEvaluationsResult = $mrgEvaluationsStmt->get_result();
        $mrgEvaluations = [];
        $totalEvaluatorMarks = 0;
        $numEvaluators = 0;
        while ($row = $mrgEvaluationsResult->fetch_assoc()) {
            $mrgEvaluations[] = [
                'evaluator_name' => $row['evaluator_name'],
                'marks' => $row['marks'],
                'remarks' => $row['remarks'],
                'sub_parts' => json_decode($row['sub_parts'], true) // Assuming sub_parts are stored as JSON
            ];
            $totalEvaluatorMarks += $row['marks'];
            $numEvaluators++;
        }

        // Calculate average evaluator marks
        $averageEvaluatorMarks = $numEvaluators ? $totalEvaluatorMarks / $numEvaluators : 0;

        // Fetch supervisor_evaluations
        $supervisorEvaluationsQuery = "
            SELECT ev.marks, ev.remarks, s.name AS supervisor_name, ev.sub_parts
            FROM supervisor_evaluations ev
            LEFT JOIN supervisors s ON ev.supervisor_id = s.id
            WHERE ev.batch_id = ? AND ev.evaluation_type = ? AND ev.student_rollno = ?
        ";
        $supervisorEvaluationsStmt = $conn->prepare($supervisorEvaluationsQuery);
        $supervisorEvaluationsStmt->bind_param('iis', $batch_id, $evaluation_type, $student['rollno']);
        $supervisorEvaluationsStmt->execute();
        $supervisorEvaluationsResult = $supervisorEvaluationsStmt->get_result();
        $supervisorEvaluations = [];
        $totalSupervisorMarks = 0;
        while ($row = $supervisorEvaluationsResult->fetch_assoc()) {
            $supervisorEvaluations[] = [
                'supervisor_name' => $row['supervisor_name'],
                'marks' => $row['marks'],
                'remarks' => $row['remarks'],
                'sub_parts' => json_decode($row['sub_parts'], true) // Assuming sub_parts are stored as JSON
            ];
            $totalSupervisorMarks += $row['marks'];
        }

        // Calculate total marks
        $totalMarks = $averageEvaluatorMarks + $totalSupervisorMarks;

        // Prepare evaluation data
        $studentEvaluations[$evaluation_type] = [
            'type_name' => $evaluationTypeNames[$evaluation_type],
            'mrg' => $mrgEvaluations,
            'supervisor' => $supervisorEvaluations,
            'average_evaluator_marks' => $averageEvaluatorMarks,
            'total_marks' => $totalMarks
        ];

        $response[$student['rollno']] = [
            'student_name' => $student['name'],
            'evaluations' => $studentEvaluations
        ];
    }
}
if ($batch_id && $evaluation_type && $type == 'DPEG') {
    // Fetch students in the batch
    $studentsQuery = "
        SELECT rollno, name
        FROM students
        WHERE batch_id = '$batch_id'
    ";
    $studentsResult = $conn->query($studentsQuery);
    $students = [];
    while ($row = $studentsResult->fetch_assoc()) {
        $students[] = $row;
    }

    // Fetch evaluations for each student
    foreach ($students as $student) {
        $studentEvaluations = [];

        // Fetch dpeg_evaluations
        $dpegEvaluationsQuery = "
            SELECT ev.marks, ev.remarks, e.name AS evaluator_name, ev.sub_parts
            FROM dpeg_evaluations ev
            LEFT JOIN dept_evaluators e ON ev.evaluator_id = e.evaluator_id
            WHERE ev.batch_id = ? AND ev.evaluation_type = ? AND ev.student_rollno = ?
        ";
        $dpegEvaluationsStmt = $conn->prepare($dpegEvaluationsQuery);
        $dpegEvaluationsStmt->bind_param('iis', $batch_id, $evaluation_type, $student['rollno']);
        $dpegEvaluationsStmt->execute();
        $dpegEvaluationsResult = $dpegEvaluationsStmt->get_result();
        $dpegEvaluations = [];
        $totalEvaluatorMarks = 0;
        $numEvaluators = 0;
        while ($row = $dpegEvaluationsResult->fetch_assoc()) {
            $dpegEvaluations[] = [
                'evaluator_name' => $row['evaluator_name'],
                'marks' => $row['marks'],
                'remarks' => $row['remarks'],
                'sub_parts' => json_decode($row['sub_parts'], true) // Assuming sub_parts are stored as JSON
            ];
            $totalEvaluatorMarks += $row['marks'];
            $numEvaluators++;
        }

        // Calculate average evaluator marks
        $averageEvaluatorMarks = $numEvaluators ? $totalEvaluatorMarks / $numEvaluators : 0;

        // Fetch supervisor_evaluations
        $supervisorEvaluationsQuery = "
            SELECT ev.marks, ev.remarks, s.name AS supervisor_name, ev.sub_parts
            FROM supervisor_evaluations ev
            LEFT JOIN supervisors s ON ev.supervisor_id = s.id
            WHERE ev.batch_id = ? AND ev.evaluation_type = ? AND ev.student_rollno = ?
        ";
        $supervisorEvaluationsStmt = $conn->prepare($supervisorEvaluationsQuery);
        $supervisorEvaluationsStmt->bind_param('iis', $batch_id, $evaluation_type, $student['rollno']);
        $supervisorEvaluationsStmt->execute();
        $supervisorEvaluationsResult = $supervisorEvaluationsStmt->get_result();
        $supervisorEvaluations = [];
        $totalSupervisorMarks = 0;
        while ($row = $supervisorEvaluationsResult->fetch_assoc()) {
            $supervisorEvaluations[] = [
                'supervisor_name' => $row['supervisor_name'],
                'marks' => $row['marks'],
                'remarks' => $row['remarks'],
                'sub_parts' => json_decode($row['sub_parts'], true) // Assuming sub_parts are stored as JSON
            ];
            $totalSupervisorMarks += $row['marks'];
        }

        // Calculate total marks
        $totalMarks = $averageEvaluatorMarks + $totalSupervisorMarks;

        // Prepare evaluation data
        $studentEvaluations[$evaluation_type] = [
            'type_name' => $evaluationTypeNames[$evaluation_type],
            'dpeg' => $dpegEvaluations,
            'supervisor' => $supervisorEvaluations,
            'average_evaluator_marks' => $averageEvaluatorMarks,
            'total_marks' => $totalMarks
        ];

        $response[$student['rollno']] = [
            'student_name' => $student['name'],
            'evaluations' => $studentEvaluations
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
