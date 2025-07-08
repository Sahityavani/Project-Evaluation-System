<?php
include('../db.php');

$batchId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$response = [];

if ($batchId > 0) {
    $batchQuery = "SELECT * FROM batches WHERE id = $batchId";
    $batchResult = $conn->query($batchQuery);
    
    if ($batchResult->num_rows > 0) {
        $batchData = $batchResult->fetch_assoc();
        $response['id'] = $batchData['id'];
        $response['batch_name'] = $batchData['batch_name'];
        $response['batch_title'] = $batchData['batch_title'];
        
        // Fetch members
        $membersQuery = "SELECT roll_no FROM batch_members WHERE batch_id = $batchId";
        $membersResult = $conn->query($membersQuery);
        $members = [];
        
        while ($member = $membersResult->fetch_assoc()) {
            $members[] = $member['roll_no'];
        }
        
        $response['members'] = $members;
    }
}

echo json_encode($response);
?>
