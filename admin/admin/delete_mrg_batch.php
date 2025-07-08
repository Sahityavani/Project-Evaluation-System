<?php
include('../db.php');

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['error' => 'No ID provided.']);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);

// Check if the mapping exists
$sql_check = "SELECT * FROM mrg_batches WHERE id = '$id'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows === 0) {
    echo json_encode(['error' => 'Mapping not found.']);
    exit;
}

// Fetch the batch ID and MRG ID for the given mapping
$mapping = $result_check->fetch_assoc();
$batch_id = $mapping['batch_id'];
$mrg_id = $mapping['mrg_id'];

// Begin transaction
$conn->begin_transaction();

try {
    
    // Set mrg_id to NULL in students
    $sql_update_students = "UPDATE students SET mrg_id = NULL WHERE mrg_id = '$mrg_id' AND batch_id = '$batch_id'";
    $conn->query($sql_update_students);

    // Set mrg_id to NULL in batches
    $sql_update_batches = "UPDATE batches SET mrg_id = NULL WHERE batch_id = '$batch_id'";
    $conn->query($sql_update_batches);

    // Delete the mapping
    $sql_delete_mapping = "DELETE FROM mrg_batches WHERE id = '$id'";
    $conn->query($sql_delete_mapping);

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => 'MRG-Batch mapping deleted successfully.']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
