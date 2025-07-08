<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs (assuming you have a secure connection established in 'db.php')
    $batchId = $conn->real_escape_string($_POST['batch_id']);
    $batchName = $conn->real_escape_string($_POST['batch_name']);
    $batchTitle = $conn->real_escape_string($_POST['batch_title']);

    $sql = "UPDATE batches SET batch_name = '$batchName', batch_title = '$batchTitle' WHERE batch_id = '$batchId'";

    if ($conn->query($sql) === TRUE) {
        $response = array('status' => 'success', 'message' => 'Batch updated successfully');
    } else {
        $response = array('status' => 'error', 'message' => 'Error updating batch: ' . $conn->error);
    }

    // Set the content type to JSON
    header('Content-Type: application/json');

    // Encode and output the JSON response
    echo json_encode($response);
}
?>
