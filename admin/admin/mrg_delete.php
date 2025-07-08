<?php
include('../db.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if MRG ID is provided
    if (isset($_POST['mrg_id']) && !empty($_POST['mrg_id'])) {
        // Sanitize input
        $mrgId = intval($_POST['mrg_id']);

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Check if MRG ID exists
            $checkMrgQuery = "SELECT COUNT(*) FROM mrg WHERE mrg_id = ?";
            if ($stmt = $conn->prepare($checkMrgQuery)) {
                $stmt->bind_param('i', $mrgId);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count === 0) {
                    throw new Exception('MRG ID does not exist');
                }
            } else {
                throw new Exception('Error preparing MRG existence check query');
            }

            // Check if MRG is associated with any batches
            $checkMrgBatchesQuery = "SELECT COUNT(*) FROM mrg_batches WHERE mrg_id = ?";
            if ($stmt = $conn->prepare($checkMrgBatchesQuery)) {
                $stmt->bind_param('i', $mrgId);
                $stmt->execute();
                $stmt->bind_result($batchCount);
                $stmt->fetch();
                $stmt->close();

                if ($batchCount > 0) {
                    throw new Exception('Cannot delete MRG as it is associated with one or more batches');
                }
            } else {
                throw new Exception('Error preparing MRG batches check query');
            }

            // Delete evaluators associated with the MRG
            $deleteEvaluatorsQuery = "DELETE FROM mrg_evaluators WHERE mrg_id = ?";
            if ($stmt = $conn->prepare($deleteEvaluatorsQuery)) {
                $stmt->bind_param('i', $mrgId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception('Error preparing delete evaluators query');
            }

            // Delete the MRG
            $deleteMrgQuery = "DELETE FROM mrg WHERE mrg_id = ?";
            if ($stmt = $conn->prepare($deleteMrgQuery)) {
                $stmt->bind_param('i', $mrgId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception('Error preparing delete MRG query');
            }

            // Commit transaction
            $conn->commit();

            // Send success response
            echo json_encode(['success' => true, 'message' => 'MRG deleted successfully']);
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();

            // Send error response
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        // Send error response for invalid request
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    // Method not allowed
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
}

$conn->close();
?>
