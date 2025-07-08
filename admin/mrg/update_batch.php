<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs (assuming you have a secure connection established in 'db.php')
    $batchId = $conn->real_escape_string($_POST['batch_id']);
    $batchName = $conn->real_escape_string($_POST['batch_name']);
    $batchTitle = $conn->real_escape_string($_POST['batch_title']);

    $sql = "UPDATE batches SET batch_name = '$batchName', batch_title = '$batchTitle' WHERE batch_id = '$batchId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect on success with a success message
        header('Location: edit-batch.php?success=Batch updated successfully');
    } else {
        // Redirect on error with an error message
        header('Location: edit-batch.php?error=Error updating batch');
    }
    exit; // Important to stop execution after the redirect
}
?>
