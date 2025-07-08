<?php
include('../db.php');

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required fields are provided
    if (isset($_POST['dpeg_id']) && !empty($_POST['dpeg_id']) &&
        isset($_POST['dpeg_name']) && !empty($_POST['dpeg_name']) &&
        isset($_POST['dpeg_title']) && !empty($_POST['dpeg_title'])) {
        
        // Sanitize input
        $dpegId = intval($_POST['dpeg_id']);
        $dpegName = $conn->real_escape_string($_POST['dpeg_name']);
        $dpegTitle = $conn->real_escape_string($_POST['dpeg_title']);
        $newEvaluators = isset($_POST['evaluators']) ? array_map('intval', $_POST['evaluators']) : [];

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update DPEG details
            $updateDpegQuery = "UPDATE dpeg SET dpeg_name = ?, dpeg_title = ? WHERE dpeg_id = ?";
            if ($stmt = $conn->prepare($updateDpegQuery)) {
                $stmt->bind_param('ssi', $dpegName, $dpegTitle, $dpegId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception('Error preparing DPEG update query');
            }

            // Fetch existing evaluators
            $existingEvaluators = [];
            $fetchEvaluatorsQuery = "SELECT evaluator_id FROM dpeg_evaluators WHERE dpeg_id = ?";
            if ($stmt = $conn->prepare($fetchEvaluatorsQuery)) {
                $stmt->bind_param('i', $dpegId);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $existingEvaluators[] = $row['evaluator_id'];
                }
                $stmt->close();
            } else {
                throw new Exception('Error preparing fetch evaluators query');
            }

            // Find evaluators to remove
            $evaluatorsToRemove = array_diff($existingEvaluators, $newEvaluators);

            // Find evaluators to add
            $evaluatorsToAdd = array_diff($newEvaluators, $existingEvaluators);

            // Check for duplicates in evaluators to add
            $duplicates = array_diff_assoc($newEvaluators, array_unique($newEvaluators));
            if (!empty($duplicates)) {
                throw new Exception('One or more evaluators were added multiple times.');
            }

            // Remove evaluators no longer assigned
            if (!empty($evaluatorsToRemove)) {
                $removeEvaluatorsQuery = "DELETE FROM dpeg_evaluators WHERE dpeg_id = ? AND evaluator_id IN (" . implode(',', $evaluatorsToRemove) . ")";
                if ($stmt = $conn->prepare($removeEvaluatorsQuery)) {
                    $stmt->bind_param('i', $dpegId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    throw new Exception('Error preparing remove evaluators query');
                }
            }

            // Add new evaluators
            if (!empty($evaluatorsToAdd)) {
                $addEvaluatorsQuery = "INSERT INTO dpeg_evaluators (dpeg_id, evaluator_id) VALUES ";
                $values = [];
                foreach ($evaluatorsToAdd as $evaluatorId) {
                    $values[] = "($dpegId, $evaluatorId)";
                }
                $addEvaluatorsQuery .= implode(',', $values);
                if (!$conn->query($addEvaluatorsQuery)) {
                    throw new Exception('Error inserting evaluators');
                }
            }

            // Commit transaction
            $conn->commit();

            // Success response
            header('Location: add-dpeg.php?success=DPEG updated successfully');
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();

            // Error response
            header('Location: add-dpeg.php?error=' . urlencode($e->getMessage()));
        }
    } else {
        // Invalid request
        header('Location: add-dpeg.php?error=Invalid request');
    }
} else {
    // Method not allowed
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
?>
