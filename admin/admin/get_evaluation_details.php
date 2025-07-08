<?php
include('auth.php');
include('../db.php');

$rollno = $_POST['rollno'];
$evaluationType = $_POST['evaluationType'];

$data = [];

// Get the evaluation type details 
$sql = "SELECT * FROM evaluation_type WHERE id = '$evaluationType'";
$result = $conn->query($sql);
$evalType = $result->fetch_assoc(); 

// MRG Evaluations (only if evaluation type is MRG)
if ($evalType['type'] == 'MRG') { 
    $sql = "SELECT me.marks, me.remarks, me.sub_parts, e.name as evaluator_name 
            FROM mrg_evaluations me
            JOIN evaluators e ON me.evaluator_id = e.evaluator_id 
            WHERE me.student_rollno = '$rollno' AND me.evaluation_type = '$evaluationType'";
    $result = $conn->query($sql);
    $data['mrgEvaluations'] = $result->fetch_all(MYSQLI_ASSOC);
}

// DPEG Evaluations (only if evaluation type is DPEG)
if ($evalType['type'] == 'DPEG') { 
    $sql = "SELECT de.marks, de.remarks, de.sub_parts, d.name as evaluator_name 
            FROM dpeg_evaluations de
            JOIN dept_evaluators d ON de.evaluator_id = d.evaluator_id
            WHERE de.student_rollno = '$rollno' AND de.evaluation_type = '$evaluationType'";
    $result = $conn->query($sql);
    $data['dpegEvaluations'] = $result->fetch_all(MYSQLI_ASSOC);
}

// Supervisor Evaluation 
$sql = "SELECT se.marks, se.remarks, se.sub_parts, su.name as supervisor_name
        FROM supervisor_evaluations se
        JOIN supervisors su ON se.supervisor_id = su.id
        WHERE se.student_rollno = '$rollno' AND se.evaluation_type = '$evaluationType'"; 
$result = $conn->query($sql);
$data['supervisorEvaluations'] = $result->fetch_all(MYSQLI_ASSOC);

$response = [
    'status' => 'success',
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);
exit;