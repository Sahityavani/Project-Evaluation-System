<?php
include('../db.php');

// Ensure the dpeg_id parameter is provided and is an integer
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['error' => 'Invalid DPEG ID']);
    exit();
}

$dpegId = intval($_POST['id']);

// Set content-type to JSON
header('Content-Type: application/json');

// Query to get DPEG details
$dpegQuery = "SELECT * FROM dpeg WHERE dpeg_id = $dpegId";
$dpegResult = $conn->query($dpegQuery);

// Check for query errors
if ($conn->error) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

if ($dpegResult->num_rows > 0) {
    $dpegData = $dpegResult->fetch_assoc();
    $response = [
        'id' => $dpegData['dpeg_id'],
        'dpeg_name' => $dpegData['dpeg_name'],
        'dpeg_title' => $dpegData['dpeg_title'],
        'evaluators' => []
    ];

    // Query to get DPEG evaluators
    $evaluatorsQuery = "SELECT evaluator_id FROM dpeg_evaluators WHERE dpeg_id = $dpegId";
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
    echo json_encode(['error' => 'DPEG not found']);
}

// Function to get evaluator name (replace with actual implementation)
function getEvaluatorName($evaluatorId) {
    global $conn;
    $query = "SELECT name FROM dept_evaluators WHERE evaluator_id = $evaluatorId"; // Assuming there's an evaluators table
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['name'];
    }
    return 'Unknown Evaluator';
}
?>
