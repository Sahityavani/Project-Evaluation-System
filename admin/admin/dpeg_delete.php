<?php
include('../db.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if DPEG ID is provided
    if (isset($_POST['dpeg_id']) && !empty($_POST['dpeg_id'])) {
        // Sanitize input
        $dpegId = intval($_POST['dpeg_id']);

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Check if DPEG ID exists
            $checkDpegQuery = "SELECT COUNT(*) FROM dpeg WHERE dpeg_id = ?";
            if ($stmt = $conn->prepare($checkDpegQuery)) {
                $stmt->bind_param('i', $dpegId);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count === 0) {
                    throw new Exception('DPEG ID does not exist');
                }
            } else {
                throw new Exception('Error preparing DPEG existence check query');
            }

            // Check if DPEG is associated with any batches
            $checkDpegBatchesQuery = "SELECT COUNT(*) FROM dpeg_batches WHERE dpeg_id = ?";
            if ($stmt = $conn->prepare($checkDpegBatchesQuery)) {
                $stmt->bind_param('i', $dpegId);
                $stmt->execute();
                $stmt->bind_result($batchCount);
                $stmt->fetch();
                $stmt->close();

                if ($batchCount > 0) {
                    throw new Exception('Cannot delete DPEG as it is associated with one or more batches');
                }
            } else {
                throw new Exception('Error preparing DPEG batches check query');
            }

            // Delete evaluators associated with the DPEG
            $deleteEvaluatorsQuery = "DELETE FROM dpeg_evaluators WHERE dpeg_id = ?";
            if ($stmt = $conn->prepare($deleteEvaluatorsQuery)) {
                $stmt->bind_param('i', $dpegId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception('Error preparing delete evaluators query');
            }

            // Delete the DPEG
            $deleteDpegQuery = "DELETE FROM dpeg WHERE dpeg_id = ?";
            if ($stmt = $conn->prepare($deleteDpegQuery)) {
                $stmt->bind_param('i', $dpegId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception('Error preparing delete DPEG query');
            }

            // Commit transaction
            $conn->commit();

            // Send success response
            echo json_encode(['success' => true, 'message' => 'DPEG deleted successfully']);
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
