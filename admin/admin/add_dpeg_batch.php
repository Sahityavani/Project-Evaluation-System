<?php

include('auth.php');
include('../db.php');

// Fetch section and dpeg_id from POST request
$id = isset($_POST['id']) ? $_POST['id'] : '';
$section = isset($_POST['section']) ? $_POST['section'] : '';
$dpeg_id = isset($_POST['dpeg_id']) ? intval($_POST['dpeg_id']) : 0;

if (!empty($section) && $dpeg_id > 0) {
    // Fetch distinct batch_id from the students table where the section matches
    $fetchBatchesQuery = "
        SELECT DISTINCT batch_id 
        FROM students 
        WHERE section = ?
    ";
    $stmt = $conn->prepare($fetchBatchesQuery);
    $stmt->bind_param("s", $section);
    $stmt->execute();
    $batchesResult = $stmt->get_result();

    $batchesToAdd = [];
    while ($batch = $batchesResult->fetch_assoc()) {
        $batch_id = intval($batch['batch_id']);

        // Ensure batch_id is not zero or invalid
        if ($batch_id > 0) {
            // Check if the batch is not already mapped to any DPEG
            $checkExistingMappingQuery = "SELECT * FROM dpeg_batches WHERE batch_id = ?";
            $stmtCheck = $conn->prepare($checkExistingMappingQuery);
            $stmtCheck->bind_param("i", $batch_id);
            $stmtCheck->execute();
            $existingMappingResult = $stmtCheck->get_result();

            if ($existingMappingResult->num_rows === 0) {
                // If not mapped, add the batch_id to the list of batches to be added
                $batchesToAdd[] = $batch_id;
            }
        }
    }

    if (!empty($batchesToAdd)) {
        foreach ($batchesToAdd as $batch_id) {
            // Insert each batch into dpeg_batches without manually handling the ID
            $insertQuery = "INSERT INTO dpeg_batches (id, batch_id, dpeg_id) VALUES (?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->bind_param("iii", $id, $batch_id, $dpeg_id);
            $stmtInsert->execute();

            // Update batches table
            $updateBatchQuery = "UPDATE batches SET dpeg_id = ? WHERE batch_id = ?";
            $stmtUpdateBatch = $conn->prepare($updateBatchQuery);
            $stmtUpdateBatch->bind_param("ii", $dpeg_id, $batch_id);
            $stmtUpdateBatch->execute();

            // Update students table
            $updateStudentsQuery = "UPDATE students SET dpeg_id = ? WHERE batch_id = ?";
            $stmtUpdateStudents = $conn->prepare($updateStudentsQuery);
            $stmtUpdateStudents->bind_param("ii", $dpeg_id, $batch_id);
            $stmtUpdateStudents->execute();
        }

        // Check if the dpeg_id condition is met before updating dpeg_evaluators
        $checkDpegEvaluatorsQuery = "SELECT * FROM dpeg_evaluators WHERE dpeg_id = ?";
        $stmtCheckEvaluators = $conn->prepare($checkDpegEvaluatorsQuery);
        $stmtCheckEvaluators->bind_param("i", $dpeg_id);
        $stmtCheckEvaluators->execute();
        $resultDpegEvaluators = $stmtCheckEvaluators->get_result();

        if ($resultDpegEvaluators->num_rows === 0) {
            $error = "No evaluators are mapped to the selected DPEG.";
            header("Location: add-dpegbatches.php?error=" . urlencode($error));
            exit;
        }

        $success = "Batches successfully mapped to DPEG.";
        header("Location: add-dpegbatches.php?success=" . urlencode($success));
    } else {
        $error = "All batches in the selected section are already mapped to DPEGs.";
        header("Location: add-dpegbatches.php?error=" . urlencode($error));
    }
} else {
    $error = "Invalid section or DPEG ID.";
    header("Location: add-dpegbatches.php?error=" . urlencode($error));
}
exit;
?>