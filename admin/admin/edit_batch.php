<?php
include('../db.php');

// Function to sanitize user inputs
function sanitize_input($data, $conn) {
    return $conn->real_escape_string(trim($data));
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete action
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $batchIdToDelete = intval($_POST['batch_id_to_delete']);

        // Check if the batch is associated with a supervisor
        $checkSupervisorQuery = "SELECT supervisor FROM batches WHERE batch_id = ? AND supervisor IS NOT NULL";
        $stmt = $conn->prepare($checkSupervisorQuery);
        $stmt->bind_param('i', $batchIdToDelete);
        $stmt->execute();
        $supervisorResult = $stmt->get_result();

        if ($supervisorResult->num_rows > 0) {
            // Batch is associated with a supervisor, cannot delete
            header('Location: add-batch.php?error=' . urlencode('Cannot delete batch as it is associated with a supervisor.'));
            exit();
        }

        // Check if the batch is associated with an MRG
        $checkMRGQuery = "SELECT mrg_id FROM mrg_batches WHERE batch_id = ?";
        $stmt = $conn->prepare($checkMRGQuery);
        $stmt->bind_param('i', $batchIdToDelete);
        $stmt->execute();
        $mrgResult = $stmt->get_result();

        if ($mrgResult->num_rows > 0) {
            // Batch is associated with an MRG, cannot delete
            header('Location: add-batch.php?error=' . urlencode('Cannot delete batch as it is associated with an MRG.'));
            exit();
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Remove batch_id from students
            $updateStudentsQuery = "UPDATE students SET batch_id = NULL WHERE batch_id = ?";
            $stmt = $conn->prepare($updateStudentsQuery);
            $stmt->bind_param('i', $batchIdToDelete);
            $stmt->execute();

            // Delete from mrg_evaluations
            $deleteMRGEvaluationsQuery = "DELETE FROM mrg_evaluations WHERE batch_id = ?";
            $stmt = $conn->prepare($deleteMRGEvaluationsQuery);
            $stmt->bind_param('i', $batchIdToDelete);
            $stmt->execute();

            // Delete from supervisor_evaluations
            $deleteSupervisorEvaluationsQuery = "DELETE FROM supervisor_evaluations WHERE batch_id = ?";
            $stmt = $conn->prepare($deleteSupervisorEvaluationsQuery);
            $stmt->bind_param('i', $batchIdToDelete);
            $stmt->execute();

            // Delete batch
            $deleteBatchQuery = "DELETE FROM batches WHERE batch_id = ?";
            $stmt = $conn->prepare($deleteBatchQuery);
            $stmt->bind_param('i', $batchIdToDelete);
            $stmt->execute();

            // Commit transaction
            $conn->commit();

            // Redirect with success message
            header('Location: add-batch.php?success=' . urlencode('Batch deleted successfully'));
            exit();
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();

            // Redirect with error message
            header('Location: add-batch.php?error=' . urlencode('Error deleting batch: ' . $e->getMessage()));
            exit();
        }
    } else if (isset($_POST['batch_id'])) {
        // Handle update action
        $batchId = intval($_POST['batch_id']);
        $batchName = sanitize_input($_POST['batch_name'], $conn);
        $batchTitle = sanitize_input($_POST['batch_title'], $conn);
        $members = isset($_POST['members']) ? array_map(function($member) use ($conn) { return sanitize_input($member, $conn); }, $_POST['members']) : [];

        // Remove duplicate members
        $uniqueMembers = array_unique($members);

        // Check for duplicates
        if (count($uniqueMembers) < count($members)) {
            $duplicateMembers = array_diff_assoc($members, $uniqueMembers);
            $duplicateMembersList = implode(', ', array_unique($duplicateMembers));
            header("Location: add-batch.php?error=" . urlencode("Duplicate members found: $duplicateMembersList"));
            exit();
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Update the batch details
            $updateBatchQuery = "UPDATE batches SET batch_name = ?, batch_title = ? WHERE batch_id = ?";
            $stmt = $conn->prepare($updateBatchQuery);
            $stmt->bind_param('ssi', $batchName, $batchTitle, $batchId);
            $stmt->execute();

            // Fetch current members for this batch
            $currentMembersQuery = "SELECT rollno FROM students WHERE batch_id = ?";
            $stmt = $conn->prepare($currentMembersQuery);
            $stmt->bind_param('i', $batchId);
            $stmt->execute();
            $currentMembersResult = $stmt->get_result();
            $currentMembers = [];
            while ($row = $currentMembersResult->fetch_assoc()) {
                $currentMembers[] = $row['rollno'];
            }

            // Determine which members need to be removed
            $membersToRemove = array_diff($currentMembers, $members);

            // Remove old members
            foreach ($membersToRemove as $rollno) {
                $updateStudentQuery = "UPDATE students SET batch_id = NULL WHERE rollno = ?";
                $stmt = $conn->prepare($updateStudentQuery);
                $stmt->bind_param('s', $rollno);
                $stmt->execute();
            }

            // Determine which members need to be added
            $membersToAdd = array_diff($members, $currentMembers);

            // Add new members
            foreach ($membersToAdd as $rollno) {
                $updateStudentQuery = "UPDATE students SET batch_id = ? WHERE rollno = ?";
                $stmt = $conn->prepare($updateStudentQuery);
                $stmt->bind_param('is', $batchId, $rollno);
                $stmt->execute();
            }

            // Update batch members count
            $batchMembersCount = count($members);
            $updateBatchMembersQuery = "UPDATE batches SET batch_members = ? WHERE batch_id = ?";
            $stmt = $conn->prepare($updateBatchMembersQuery);
            $stmt->bind_param('ii', $batchMembersCount, $batchId);
            $stmt->execute();

            // Commit transaction
            $conn->commit();

            // Redirect with success message
            header('Location: add-batch.php?success=' . urlencode('Batch updated successfully'));
            exit();
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();

            // Redirect with error message
            header('Location: add-batch.php?error=' . urlencode('Error updating batch: ' . $e->getMessage()));
            exit();
        }
    }
}
?>
