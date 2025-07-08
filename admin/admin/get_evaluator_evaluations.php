<?php
include('auth.php');
include('../db.php');

$evaluatorId = $_POST['evaluatorId'];
$evaluationTypeId = $_POST['evaluationTypeId'];

// Get the evaluation type to determine the correct table to query
$sql = "SELECT * FROM evaluation_type WHERE id = '$evaluationTypeId'";
$result = $conn->query($sql);
$evalType = $result->fetch_assoc(); 

if ($evalType['type'] == 'MRG') {
    $tableName = 'mrg_evaluations'; // Query MRG evaluations table
} elseif ($evalType['type'] == 'DPEG') {
    $tableName = 'dpeg_evaluations'; // Query DPEG evaluations table
} else {
    // Handle cases where the type is not MRG or DPEG (e.g., supervisor, other)
    $response = [
        'status' => 'error',
        'message' => 'Invalid evaluation type.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$sql = "SELECT 
            me.marks, 
            me.remarks, 
            me.sub_parts,
            s.rollno AS student_rollno,
            s.name AS student_name
        FROM 
            $tableName me
        JOIN 
            students s ON me.student_rollno = s.rollno
        WHERE 
            me.evaluator_id = ? AND me.evaluation_type = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $evaluatorId, $evaluationTypeId);
$stmt->execute();
$result = $stmt->get_result();

$evaluations = $result->fetch_all(MYSQLI_ASSOC);

$response = [
    'status' => 'success',
    'data' => $evaluations
];

header('Content-Type: application/json');
echo json_encode($response);
exit;