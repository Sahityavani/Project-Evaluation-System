



<?php
include('auth.php');
include('../db.php');

// Fetch batch_id and mrg_id from POST request
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$batch_id = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;
$mrg_id = isset($_POST['mrg_id']) ? intval($_POST['mrg_id']) : 0;

if ($batch_id > 0 && $mrg_id > 0) {
    // Check if the batch is already mapped to an MRG
    $checkMappingQuery = "SELECT * FROM mrg_batches WHERE batch_id = ? AND mrg_id = ?";
    $stmt = $conn->prepare($checkMappingQuery);
    $stmt->bind_param("ii", $batch_id, $mrg_id);
    $stmt->execute();
    $mappingResult = $stmt->get_result();

    if ($mappingResult->num_rows === 0) {
        // Check if the batch is already mapped to a different MRG
        $checkExistingMappingQuery = "SELECT * FROM mrg_batches WHERE batch_id = ?";
        $stmt = $conn->prepare($checkExistingMappingQuery);
        $stmt->bind_param("i", $batch_id);
        $stmt->execute();
        $existingMappingResult = $stmt->get_result();

        if ($existingMappingResult->num_rows > 0) {
            $error = "This batch is already mapped to another MRG.";
            header("Location: add-mrgbatches.php?error=" . urlencode($error));
            exit;
        }

        // Insert into mrg_batches
        $insertQuery = "INSERT INTO mrg_batches (id, batch_id, mrg_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iii", $id, $batch_id, $mrg_id);
        $stmt->execute();

        // Update batches table
        $updateBatchQuery = "UPDATE batches SET mrg_id = ? WHERE batch_id = ?";
        $stmt = $conn->prepare($updateBatchQuery);
        $stmt->bind_param("ii", $mrg_id, $batch_id);
        $stmt->execute();

        // Update students table
        $updateStudentsQuery = "UPDATE students SET mrg_id = ? WHERE batch_id = ?";
        $stmt = $conn->prepare($updateStudentsQuery);
        $stmt->bind_param("ii", $mrg_id, $batch_id);
        $stmt->execute();

        // Check if the mrg_id condition is met before updating mrg_evaluators
        $checkMrgEvaluatorsQuery = "SELECT * FROM mrg_evaluators WHERE mrg_id = ?";
        $stmt = $conn->prepare($checkMrgEvaluatorsQuery);
        $stmt->bind_param("i", $mrg_id);
        $stmt->execute();
        $resultMrgEvaluators = $stmt->get_result();

        if ($resultMrgEvaluators->num_rows === 0) {
            $error = "No evaluators are mapped to the selected MRG.";
            header("Location: add-mrgbatches.php?error=" . urlencode($error));
            exit;
        }

        $success = "Batch successfully mapped to MRG.";
        header("Location: add-mrgbatches.php?success=" . urlencode($success));
    } else {
        $error = "This batch is already mapped to the selected MRG.";
        header("Location: add-mrgbatches.php?error=" . urlencode($error));
    }
} else {
    $error = "Invalid batch ID or MRG ID.";
    header("Location: add-mrgbatches.php?error=" . urlencode($error));
}
exit;
?>
