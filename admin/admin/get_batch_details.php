<?php
include('../db.php');

// Ensure the id parameter is provided and is an integer
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['error' => 'Invalid ID']);
    exit();
}

$batchId = intval($_POST['id']);

// Set content-type to JSON
header('Content-Type: application/json');

// Query to get batch details
$batchQuery = "SELECT * FROM batches WHERE batch_id = $batchId";
$batchResult = $conn->query($batchQuery);

// Check for query errors
if ($conn->error) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

if ($batchResult->num_rows > 0) {
    $batchData = $batchResult->fetch_assoc();
    $response = [
        'id' => $batchData['batch_id'],
        'batch_name' => $batchData['batch_name'],
        'batch_title' => $batchData['batch_title'],
        'members' => []
    ];

    // Query to get batch members
    $membersQuery = "SELECT rollno, name FROM students WHERE batch_id = $batchId";
    $membersResult = $conn->query($membersQuery);

    // Check for query errors
    if ($conn->error) {
        echo json_encode(['error' => 'Database query error']);
        exit();
    }

    while ($member = $membersResult->fetch_assoc()) {
        $response['members'][] = [
            'rollno' => $member['rollno'],
            'name' => $member['name']
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Batch not found']);
}
?>
