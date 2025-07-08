<?php
include('../db.php');

// Ensure the mrg_id parameter is provided and is an integer
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['error' => 'Invalid MRG ID']);
    exit();
}

$mrgId = intval($_POST['id']);

// Set content-type to JSON
header('Content-Type: application/json');

// Query to get MRG details
$mrgQuery = "SELECT * FROM mrg WHERE mrg_id = $mrgId";
$mrgResult = $conn->query($mrgQuery);

// Check for query errors
if ($conn->error) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

if ($mrgResult->num_rows > 0) {
    $mrgData = $mrgResult->fetch_assoc();
    $response = [
        'id' => $mrgData['mrg_id'],
        'mrg_name' => $mrgData['mrg_name'],
        'mrg_title' => $mrgData['mrg_title'],
        'evaluators' => []
    ];

    // Query to get MRG evaluators
    $evaluatorsQuery = "SELECT evaluator_id FROM mrg_evaluators WHERE mrg_id = $mrgId";
    $evaluatorsResult = $conn->query($evaluatorsQuery);

    // Check for query errors
    if ($conn->error) {
        echo json_encode(['error' => 'Database query error']);
        exit();
    }

    while ($evaluator = $evaluatorsResult->fetch_assoc()) {
        $response['evaluators'][] = [
            'evaluator_id' => $evaluator['evaluator_id'],
            // Assuming you have a way to get evaluator names
            'evaluator_name' => getEvaluatorName($evaluator['evaluator_id'])
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'MRG not found']);
}

// Function to get evaluator name (replace with actual implementation)
function getEvaluatorName($evaluatorId) {
    global $conn;
    $query = "SELECT name FROM evaluators WHERE evaluator_id = $evaluatorId"; // Assuming there's an evaluators table
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['name'];
    }
    return 'Unknown Evaluator';
}
?>
