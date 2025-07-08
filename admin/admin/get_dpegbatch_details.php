<?php
include('../db.php');

$dpeg_id = isset($_POST['dpeg_id']) ? intval($_POST['dpeg_id']) : 0;

if ($dpeg_id <= 0) {
    echo 'Invalid DPEG ID';
    exit;
}

// Fetch all batches associated with the dpeg_id
$batchesQuery = "
    SELECT DISTINCT db.batch_id, b.batch_name, b.batch_title
    FROM dpeg_batches db
    JOIN batches b ON db.batch_id = b.batch_id
    WHERE db.dpeg_id = ?
";
$stmt = $conn->prepare($batchesQuery);
$stmt->bind_param('i', $dpeg_id);
$stmt->execute();
$batchesResult = $stmt->get_result();

$batches = [];
while ($batch = $batchesResult->fetch_assoc()) {
    $batches[$batch['batch_id']] = $batch;
}

if (empty($batches)) {
    echo 'No batches found for this DPEG ID.';
    exit;
}

// Fetch batch members for each batch
$batchMembersQuery = "
    SELECT s.batch_id, s.rollno, s.name
    FROM students s
    WHERE s.batch_id IN (" . implode(',', array_fill(0, count($batches), '?')) . ")
";
$stmt = $conn->prepare($batchMembersQuery);
$stmt->bind_param(str_repeat('i', count($batches)), ...array_keys($batches));
$stmt->execute();
$batchMembersResult = $stmt->get_result();

// Fetch evaluator IDs for the dpeg_id
$evaluatorIdsQuery = "
    SELECT evaluator_id
    FROM dpeg_evaluators
    WHERE dpeg_id = ?
";
$stmt = $conn->prepare($evaluatorIdsQuery);
$stmt->bind_param('i', $dpeg_id);
$stmt->execute();
$evaluatorIdsResult = $stmt->get_result();

$evaluatorIds = [];
while ($evaluatorId = $evaluatorIdsResult->fetch_assoc()) {
    $evaluatorIds[] = $evaluatorId['evaluator_id'];
}

if (empty($evaluatorIds)) {
    echo 'No evaluators found for this DPEG ID.';
    exit;
}

// Fetch evaluator details using the evaluator IDs
$evaluatorsQuery = "
    SELECT e.evaluator_id, e.name
    FROM dept_evaluators e
    WHERE e.evaluator_id IN (" . implode(',', array_fill(0, count($evaluatorIds), '?')) . ")
";
$stmt = $conn->prepare($evaluatorsQuery);
$stmt->bind_param(str_repeat('i', count($evaluatorIds)), ...$evaluatorIds);
$stmt->execute();
$evaluatorsResult = $stmt->get_result();

// Prepare output for batches and their students
$output = '';
$studentsByBatch = [];
while ($student = $batchMembersResult->fetch_assoc()) {
    $studentsByBatch[$student['batch_id']][] = $student;
}

foreach ($batches as $batchId => $batch) {
    $batchName = htmlspecialchars($batch['batch_name']);
    $batchTitle = htmlspecialchars($batch['batch_title']);
    
    // Output batch details
    $output .= '<h5>Batch Details</h5>';
    $output .= '<p><strong>Batch ID:</strong> ' . htmlspecialchars($batchId) . '</p>';
    $output .= '<p><strong>Batch Name:</strong> ' . $batchName . '</p>';
    $output .= '<p><strong>Batch Title:</strong> ' . $batchTitle . '</p>';

    // Output students for the current batch
    if (isset($studentsByBatch[$batchId]) && !empty($studentsByBatch[$batchId])) {
        $output .= '<h6>Students in Batch ' . $batchName . '</h6>';
        $output .= '<table class="table table-striped">';
        $output .= '<thead><tr><th>Roll Number</th><th>Name</th></tr></thead><tbody>';
        
        foreach ($studentsByBatch[$batchId] as $student) {
            $output .= '<tr>';
            $output .= '<td>' . htmlspecialchars($student['rollno']) . '</td>';
            $output .= '<td>' . htmlspecialchars($student['name']) . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</tbody></table>';
    } else {
        $output .= '<p>No students found for this batch.</p>';
    }
}

// Prepare output for evaluators
$output .= '<h5>Evaluators</h5>';
$output .= '<table class="table table-striped">';
$output .= '<thead><tr><th>Evaluator ID</th><th>Evaluator Name</th></tr></thead><tbody>';

while ($evaluator = $evaluatorsResult->fetch_assoc()) {
    $output .= '<tr>';
    $output .= '<td>' . htmlspecialchars($evaluator['evaluator_id']) . '</td>';
    $output .= '<td>' . htmlspecialchars($evaluator['name']) . '</td>';
    $output .= '</tr>';
}

$output .= '</tbody></table>';

echo $output;
?>
